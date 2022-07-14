<?php

namespace enrol_ethos\services;

use enrol_ethos\entities\reports\report_action;
use enrol_ethos\entities\reports\report_run;
use enrol_ethos\ethosclient\service\curriculum_lookup_service;
use enrol_ethos\ethosclient\service\ethos_person_provider;
use enrol_ethos\ethosclient\service\ethos_student_academic_program_provider;
use enrol_ethos\ethosclient\service\messages_model;
use enrol_ethos\repositories\db_course_category_repository;
use enrol_ethos\repositories\db_course_repository;
use enrol_ethos\repositories\db_user_repository;

class processing_service {

    private student_lookup_service $studentLookupService;
    private curriculum_lookup_service $curriculumLookupService;
    private alternative_credential_service $alternativeCredentialService;
    private ethos_person_provider $personService;
    private ethos_student_academic_program_provider $studentAcademicProgramService;

    private $courseCategoryRepository;
    private $courseRepository;
    private $courseService;

    private $userRepository;
    private $userService;

    private $trace;

    public function __construct($trace)
    {
        global $DB;

        $this->personService = ethos_person_provider::getInstance();
        $this->studentAcademicProgramService = ethos_student_academic_program_provider::getInstance();

        $this->alternativeCredentialService = new alternative_credential_service();
        $this->studentLookupService = new student_lookup_service($trace);
        $this->curriculumLookupService = new curriculum_lookup_service();
        $this->courseRepository = new db_course_repository($DB);
        $this->courseCategoryRepository = new db_course_category_repository($DB);
        $this->courseService = new course_service($this->courseRepository, $this->courseCategoryRepository);
        $this->userRepository = new db_user_repository($DB);
        $this->userService = new user_service($this->userRepository, $this->courseService);

        $this->trace = $trace;
    }

    public function process_user_by_id ($id) {
        $user = $this->userService->getUserById($id);

        if ($user) {
            $this->process_user($user);
        }
    }

    public function process_new_users() {
        //Get list of users to work with.
        $this->trace->output('Processing users without Banner IDs...');

        $users = $this->userService->getUserProfilesWithoutBannerIds();
        $this->process_users($users);

        $this->trace->output('...finished processing users without Banner IDs');
        $this->trace->finished();
    }

//    public function process_all_users() {
//        $this->trace->output('Caching all reference values...');
//        $time_start = microtime(true);
//        $this->ethosClient->cacheAllReferenceTypes();
//        $time_end = microtime(true);
//        $time = $time_end - $time_start;
//        $this->trace->output("...finished caching values in $time seconds");
//
//        $this->trace->output('Caching all person records');
//        $time_start = microtime(true);
//        $this->ethosClient->cacheAllPersonRecords();
//        $time_end = microtime(true);
//        $time = $time_end - $time_start;
//        $this->trace->output("...finished caching person records in $time seconds");
//
//        $this->trace->output('Processing all users...');
//        $time_start = microtime(true);
//        if ($users = $this->userService->getUsersByAuthType('ldap')) {
//            $time_end = microtime(true);
//            $time = $time_end - $time_start;
//            $count = count($users);
//            $this->trace->output("Found $count users in Moodle to process in $time seconds");
//
//            $time_start = microtime(true);
//            $this->process_users($users);
//            $time_end = microtime(true);
//            $time = $time_end - $time_start;
//            $this->trace->output("...finished processing all users in $time seconds");
//        }
//
//        $this->trace->finished();
//    }

//    /**
//     * @param report_run $report
//     * @param message_model[] $persons
//     * @return report_action[]
//     */
//    public function process_person_updates(report_run $report, array $persons) : array {
//        $reportActions = array();
//
//        if(!isset($persons) || count($persons) == 0) {
//            return $reportActions;
//        }
//
//        $personIds = array_unique(array_column($persons, '$personId'));
//        $existingPersonIds = array();
//
//        $moodleUsersWithMatchingPersonIds = $this->userService->getUserProfilesWithBannerIds($personIds);
//        foreach ($moodleUsersWithMatchingPersonIds as $user) {
//            $updatedPersonIds[] = $user->userProfile->bannerGuid;
//
//            // Update
//        }
//
//        $personIds = array_diff($personIds, $existingPersonIds);
//        if(count($personIds) == 0) {
//            return $reportActions;
//        }
//
//        $employeeNumberAlternativeCredentialType = $this->alternativeCredentialService->getEmployeeNumberAlternativeCredentialType();
//        foreach ($personIds as $personId) {
//            $person = $this->personService->getPersonById($personId);
//
//            if(!$this->alternativeCredentialService->hasAlternativeCredentialOfType($person, $employeeNumberAlternativeCredentialType)) {
//                continue;
//            }
//
//            // Create
//        }
//    }

    /**
     * @param report_run $report
     * @param messages_model $messagesModel
     * @return report_action[]
     */
    public function process_ethos_updates(report_run $report, messages_model $messagesModel) : array {
        $bannerGuidsFromEthos = array();

        $employeeNumberAlternativeCredentialType = $this->alternativeCredentialService->getEmployeeNumberAlternativeCredentialType();
        if(isset($messagesModel->persons) && count($messagesModel->persons) > 0) {
            foreach ($messagesModel->persons as $messageModel) {
                $person = $this->personService->getPersonById($messageModel->personId);
                if(!$this->alternativeCredentialService->hasAlternativeCredentialOfType($person, $employeeNumberAlternativeCredentialType)) {
                    continue;
                }

                if (in_array($messageModel->personId, $bannerGuidsFromEthos)) {
                    continue;
                }

                $bannerGuidsFromEthos[] = $messageModel->personId;
            }
        }

        if(isset($messagesModel->studentAcademicPrograms) && count($messagesModel->studentAcademicPrograms) > 0) {
            foreach ($messagesModel->studentAcademicPrograms as $messageModel) {
                $studentAcademicProgram = $this->studentAcademicProgramService->getStudentAcademicProgram($messageModel->resourceId);
                if(!isset($studentAcademicProgram)
                    || !isset($studentAcademicProgram->obu_SorlcurCactCode)
                    || $studentAcademicProgram->obu_SorlcurCactCode !== 'ACTIVE'
                    || !isset($studentAcademicProgram->obu_SorlcurLmodCode)
                    || $studentAcademicProgram->obu_SorlcurLmodCode !== 'LEARNER') {
                    continue;
                }

                if (!in_array($messageModel->personId, $bannerGuidsFromEthos))
                {
                    continue;
                }

                $bannerGuidsFromEthos[] = $messageModel->personId;
            }
        }

        if(!$bannerGuidsFromEthos) {
            return array();
        }

        $moodleUsersWithMatchingBannerGuid = $this->userService->getUserProfilesWithBannerIds($bannerGuidsFromEthos);
        $existingBannerGuids = array();
        foreach ($moodleUsersWithMatchingBannerGuid as $user) {
            $bannerGuid = $user->userProfile->bannerGuid;
            if (!in_array($bannerGuid, $existingBannerGuids))
            {
                $existingBannerGuids[] = $bannerGuid;
            }

            // TODO : Process user
            //$this->process_user($user);
        }

        $bannerGuidsNotInMoodle = array_diff($bannerGuidsFromEthos, $existingBannerGuids);

        if(!$bannerGuidsNotInMoodle) {
            return array();
        }

        $reportActions = array();
        $moodleUsersWithoutMatchingBannerGuid = array();

        foreach($bannerGuidsNotInMoodle as $bannerGuid) {
            $bannerPerson = $this->personService->getPersonById($bannerGuid);
            if(!$bannerPerson) {
                continue;
            }

            // TODO : Confirm name and process
            $bannerFirstName = $bannerPerson->names[0]->firstName ?? "Unknown";
            $bannerLastName = $bannerPerson->names[0]->lastName ?? "Unknown";
            $username = $this->getBannerIdFromEthosPerson($bannerPerson);

            if($this->alternativeCredentialService->hasAlternativeCredentialOfType($bannerPerson, $employeeNumberAlternativeCredentialType)) {
                $username = strtolower($this->alternativeCredentialService->getAlternativeCredentialOfType($bannerPerson, $employeeNumberAlternativeCredentialType));
            }

            // check user in Moodle
            $moodleUserWithoutBannerGuid = $this->userService->getUserByUsername($username);
            if($moodleUserWithoutBannerGuid) {
                // Set banner guid
                $moodleUserWithoutBannerGuid->userProfile->bannerGuid = $bannerGuid;
                $this->userService->updateUserProfile($moodleUserWithoutBannerGuid);

                $reportActions[] = new report_action("update", "user", $bannerGuid, $username);
                $report->incrementUsersUpdated();
            }
            else {
                // Create stub user
                $moodleUserWithoutBannerGuid = $this->userService->createUser(
                    $username,
                    $bannerFirstName,
                    $bannerLastName,
                    $username . '@brookes.ac.uk');

                $moodleUserWithoutBannerGuid->userProfile->bannerGuid = $bannerGuid;
                $this->userService->updateUserProfile($moodleUserWithoutBannerGuid);

                $reportActions[] = new report_action("create", "user", $bannerGuid, $username);
                $report->incrementUsersCreated();
            }

            array_push($moodleUsersWithoutMatchingBannerGuid, $moodleUserWithoutBannerGuid);
        }

//        // TODO : Process user
//        foreach ($moodleUsersWithoutMatchingBannerGuid as $user) {
//            $this->process_user($user);
//        }

        return $reportActions;
    }

    private function getBannerIdFromEthosPerson($bannerPerson) : string {
        if(!$bannerPerson) return '';

        $credentials = $bannerPerson->credentials;
        if(!$credentials) return '';

        $bannerId = '';
        foreach($credentials as $credential) {
            $type = $credential->type;
            if($type == 'bannerId') {
                $bannerId = $credential->value;
                break;
            }
        }

        return $bannerId;
    }

    public function create_psuedo_courses() {

        $this->trace->output("Create psuedo courses...");

        // Getting all programmes can take a while
        ini_set('max_execution_time', 600);

        //Get list of programmes to work with.
        $programmes = $this->curriculumLookupService->getAllAcademicPrograms();

        $count = count($programmes);
        $this->trace->output("Found $count active programmes");

        //Loop through each and create a moodle course for the year*
        foreach ($programmes as $programme) {

            $idnumber = $this->getMoodleCourseIdNumber($programme->guid, $programme->academicLevelCode);

            $data = [
            'idnumber' => $idnumber,
            'oldidnumber' => $programme->guid,
            'shortname' => $programme->courseCode,
            'name' => $programme->courseTitle,
            'categories' => array($programme->facultyCode)
            ];

            $this->courseService->updateOrCreateCourse($data);
            $this->trace->output("Processed: $programme->courseCode");
        }

        $this->trace->output("...Finished creating psuedo courses");
        $this->trace->finished();
    }

    private function getMoodleCourseIdNumber($guid, $academicLevel) {
        return "$academicLevel--$guid";
    }

    private function process_users($users) {
        $count = count($users);
        $this->trace->output("Processing $count users");

        //Loop through each and fill in the missing profile fields
        foreach ($users as $user) {
            $this->process_user($user);
        }
    }

    private function process_user($user) {

        $this->trace->output("Processing user with username: '$user->username'");

        $time_start_ethos = microtime(true);

        if ($student = $this->getEthosStudent($user)) {

            $time_end = microtime(true);
            $time = $time_end - $time_start_ethos;

            $this->trace->output("Found Ethos user - Banner GUID: {$student->personId} in $time seconds");

            $time_start = microtime(true);
            $this->fill_user_profile($student, $user);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $this->trace->output("Filled user profile in $time seconds");

            $time_start = microtime(true);
            $this->userService->addDefaultEnrolments($user);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $this->trace->output("Added default enrolments in $time seconds");

            $time_start = microtime(true);
            $this->userService->updateUserProfile($user);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $this->trace->output("Updated user profile in $time seconds");

        } else {
            $this->trace->output("No ethos user found");
        }
    }

    private function getEthosStudent($user) {
        if (isset($user->userProfile->bannerGuid) && $user->userProfile->bannerGuid) {
            $this->trace->output("Using GUID: '{$user->userProfile->bannerGuid}'");
            return $this->studentLookupService->lookupStudentFromPersonId($user->userProfile->bannerGuid);
        }

        if (isset($user->username)) {
            $this->trace->output("Using Username: '{$user->username}'");
            return $this->studentLookupService->lookupStudentFromBannerId($user->username);
        }

        return null;
    }

    private function fill_user_profile($student, $user) {

        if ($student && $user) {
            $userProfile = $user->userProfile;
            $userProfile->courseCode = $student->courseCode;
            $userProfile->courseTitle = $student->courseTitle;
            $userProfile->facultyCode = $student->facultyCode;
            $userProfile->facultyTitle = $student->facultyTitle;
            $userProfile->departmentCode = $student->departmentCode;
            $userProfile->departmentTitle = $student->departmentTitle;
            $userProfile->schoolTypeCode = $student->schoolTypeCode;
            $userProfile->schoolTypeTitle = $student->schoolTypeTitle;
            $userProfile->attendanceMode = $student->attendanceMode;
            $userProfile->attendanceModeTitle = $student->attendanceModeTitle;
            $userProfile->subjectCode = $student->subjectCode;
            $userProfile->subjectTitle = $student->subjectTitle;
            $userProfile->awardCode = $student->awardCode;
            $userProfile->awardTitle = $student->awardTitle;
            $userProfile->startDate = $student->startDate;
            $userProfile->endDate = $student->endDate;
            $userProfile->status = $student->status;
            $userProfile->statusTitle = $student->statusTitle;
            $userProfile->graduatedOn = $student->graduatedOn;
            $userProfile->recognitions = $student->recognitions;
            $userProfile->creditsEarned = $student->creditsEarned;
            $userProfile->academicLevel = $student->academicLevel;
            $userProfile->programmes = $student->programmes;
            $userProfile->leadProgramOfStudy = $student->leadProgramOfStudy;
            $userProfile->dyslexic = $student->dyslexic;
        }

    }
}
