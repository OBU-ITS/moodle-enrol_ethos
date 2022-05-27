<?php

namespace enrol_ethos\services;

use enrol_ethos\ethosclient\client\ethos_client;
use enrol_ethos\entities\user;
use enrol_ethos\entities\user_profile;
use enrol_ethos\ethosclient\service\messages_model;
use enrol_ethos\ethosclient\service\message_model;

class processing_service {

    private $ethosApiKey;
    private $ethosClient;
    private $studentLookupService;
    private $curriculumLookupService;

    private $courseCategoryRepository;
    private $courseRepository;
    private $courseService;

    private $userRepository;
    private $userService;

    private $trace;

    public function __construct($trace)
    {
        global $DB, $CFG;

        $this->ethosApiKey = $this->getApiKey();
        $this->ethosClient = new \enrol_ethos\ethosclient\client\ethos_client($this->ethosApiKey);
        $this->studentLookupService = new \enrol_ethos\ethosclient\service\student_lookup_service($this->ethosClient, $trace);
        $this->curriculumLookupService = new \enrol_ethos\ethosclient\service\curriculum_lookup_service($this->ethosClient);
        $this->courseRepository = new \enrol_ethos\repositories\db_course_repository($DB, $CFG);
        $this->courseCategoryRepository = new \enrol_ethos\repositories\db_course_category_repository($DB);
        $this->courseService = new \enrol_ethos\services\course_service($this->courseRepository, $this->courseCategoryRepository);
        $this->userRepository = new \enrol_ethos\repositories\db_user_repository($DB);
        $this->userService = new \enrol_ethos\services\user_service($this->userRepository, $this->courseService);

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

    public function process_all_users() {
        $this->trace->output('Caching all reference values...');
        $time_start = microtime(true);
        $this->ethosClient->cacheAllReferenceTypes();
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $this->trace->output("...finished caching values in $time seconds");

        $this->trace->output('Caching all person records');
        $time_start = microtime(true);
        $this->ethosClient->cacheAllPersonRecords();
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $this->trace->output("...finished caching person records in $time seconds");

        $this->trace->output('Processing all users...');
        $time_start = microtime(true);
        if ($users = $this->userService->getUsersByAuthType('ldap')) {
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $count = count($users);
            $this->trace->output("Found $count users in Moodle to process in $time seconds");

            $time_start = microtime(true);
            $this->process_users($users);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $this->trace->output("...finished processing all users in $time seconds");
        }

        $this->trace->finished();
    }

    public function process_ethos_updates($lastProcessedID = 0, $maxProcessedID = 0) {

        $messagesModel = $this->studentLookupService->getStudentsWithChanges($lastProcessedID, $maxProcessedID);

        $this->trace->output("----------------------------------------");

        $bannerGuidsFromEthos = array();

        $employeeNumberAlternativeCredentialType = $this->ethosClient->getEmployeeNumberAlternativeCredentialType();
        if(isset($messagesModel->persons) && count($messagesModel->persons) > 0) {
            $time_start = microtime(true);
            $this->trace->output("Processing person messages started.");

            $personsDiscardedCount = 0;
            $personsProcessedCount = 0;
            $personsDuplicateCount = 0;
            foreach ($messagesModel->persons as $messageModel) {
                $person = $this->ethosClient->getPersonById($messageModel->personId);
                if(!$this->hasAlternativeCredentialOfType($person, $employeeNumberAlternativeCredentialType)) {
                    $personsDiscardedCount++;
                    continue;
                }

                if (!in_array($messageModel->personId, $bannerGuidsFromEthos)) {
                    $personsProcessedCount++;
                    $this->trace->output("Staff identified with person change (PersonGuid: {$messageModel->personId})");
                    $bannerGuidsFromEthos[] = $messageModel->personId;
                }
                else {
                    $personsDuplicateCount++;
                }
            }
            $this->trace->output("Discarded: $personsDiscardedCount (No Employee Number).");
            $this->trace->output("Duplicates: $personsDuplicateCount.");
            $this->trace->output("Processed: $personsProcessedCount.");

            $time_end = microtime(true);
            $time = round($time_end - $time_start, 2, PHP_ROUND_HALF_UP);
            $this->trace->output("Processing Person messages finished in $time seconds.");

            $this->trace->output("----------------------------------------");
        }


        if(isset($messagesModel->studentAcademicPrograms) && count($messagesModel->studentAcademicPrograms) > 0) {
            $time_start = microtime(true);
            $this->trace->output("Processing Student Academic Program messages started.");
            $studentAcademicProgramsDiscardedCount = 0;
            $studentAcademicProgramsProcessedCount = 0;
            $studentAcademicProgramsDuplicateCount = 0;
            foreach ($messagesModel->studentAcademicPrograms as $messageModel) {
                $studentAcademicProgram = $this->ethosClient->getStudentAcademicProgram($messageModel->resourceId);
                if(!isset($studentAcademicProgram)
                    || !isset($studentAcademicProgram->obu_SorlcurCactCode)
                    || $studentAcademicProgram->obu_SorlcurCactCode !== 'ACTIVE'
                    || !isset($studentAcademicProgram->obu_SorlcurLmodCode)
                    || $studentAcademicProgram->obu_SorlcurLmodCode !== 'LEARNER') {
                    $studentAcademicProgramsDiscardedCount++;
                    continue;
                }

                if (!in_array($messageModel->personId, $bannerGuidsFromEthos))
                {
                    $studentAcademicProgramsProcessedCount++;
                    $this->trace->output("Student identified with program change (PersonGuid: {$messageModel->personId})");
                    $bannerGuidsFromEthos[] = $messageModel->personId;
                }
                else{
                    $studentAcademicProgramsDuplicateCount++;
                }
            }
            $this->trace->output("Discarded: $studentAcademicProgramsDiscardedCount (Not ACTIVE and LEARNER).");
            $this->trace->output("Duplicates: $studentAcademicProgramsDuplicateCount.");
            $this->trace->output("Processed: $studentAcademicProgramsProcessedCount.");

            $time_end = microtime(true);
            $time = round($time_end - $time_start, 2, PHP_ROUND_HALF_UP);
            $this->trace->output("Processing Student Academic Program messages finished in $time seconds.");

            $this->trace->output("----------------------------------------");
        }

        if(!$bannerGuidsFromEthos) {
            $this->trace->output("Finished reading messages from ethos queue.");
            $this->trace->finished();
            return;
        }

        $moodleUsersWithMatchingBannerGuid = $this->userService->getUserProfilesWithBannerIds($bannerGuidsFromEthos);
        $count = count($moodleUsersWithMatchingBannerGuid);
        $this->trace->output("Found $count Moodle users that have matching Banner GUIDs");
        $this->trace->output("----------------------------------------");
        $existingBannerGuids = array();
        foreach ($moodleUsersWithMatchingBannerGuid as $user) {
            $bannerGuid = $user->userProfile->bannerGuid;
            if (!in_array($bannerGuid, $existingBannerGuids))
            {
                $existingBannerGuids[] = $bannerGuid;
            }
            // TODO : Process user
            //$this->process_user($user);

            //$this->trace->output("----------------------------------------");
        }

        $bannerGuidsNotInMoodle = array_diff($bannerGuidsFromEthos, $existingBannerGuids);

        if(!$bannerGuidsNotInMoodle) {
            $this->trace->output("Finished reading messages from ethos queue");
            $this->trace->finished();
            return;
        }

        $count = count($bannerGuidsNotInMoodle);
        $this->trace->output("Found $count persons with changes which cannot be found in Moodle");
        $this->trace->output("----------------------------------------");

        $moodleUsersWithoutMatchingBannerGuid = array();
        foreach($bannerGuidsNotInMoodle as $bannerGuid) {
            $bannerPerson = $this->ethosClient->getPersonById($bannerGuid);
            if(!$bannerPerson) {
                $this->trace->output("Cannot find ($bannerGuid) within Ethos");
                continue;
            }

            $bannerFirstName = $bannerPerson->names[0]->firstName ?? "Unknown";
            $bannerLastName = $bannerPerson->names[0]->lastName ?? "Unknown";
            $username = $this->getBannerIdFromEthosPerson($bannerPerson);

            if($this->hasAlternativeCredentialOfType($bannerPerson, $employeeNumberAlternativeCredentialType)) {
                $username = strtolower($this->getAlternativeCredentialOfType($bannerPerson, $employeeNumberAlternativeCredentialType));
            }

            // check user in Moodle
            $moodleUserWithoutBannerGuid = $this->userService->getUserByUsername($username);
            if($moodleUserWithoutBannerGuid) {
                // Set banner guid
                $moodleUserWithoutBannerGuid->userProfile->bannerGuid = $bannerGuid;
                $this->userService->updateUserProfile($moodleUserWithoutBannerGuid);

                $this->trace->output("User updated Banner Guid ($bannerGuid). $bannerFirstName $bannerLastName ($username)");
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

                $this->trace->output("User created ($bannerGuid). $bannerFirstName $bannerLastName ($username)");
            }

            array_push($moodleUsersWithoutMatchingBannerGuid, $moodleUserWithoutBannerGuid);
        }

//        $this->trace->output("----------------------------------------");
//        // TODO : Process user
//        foreach ($moodleUsersWithoutMatchingBannerGuid as $user) {
//            $this->process_user($user);
//
//            $this->trace->output("----------------------------------------");
//        }


        $this->trace->output("Finished reading messages from ethos queue");
        $this->trace->finished();
    }

    private function getAlternativeCredentialOfType($peron, $alternativeCredentialType) : string {
        if(!isset($peron) || !isset($peron->alternativeCredentials) || !isset($alternativeCredentialType) || !isset($alternativeCredentialType->id)) {
            return '';
        }

        foreach ($peron->alternativeCredentials as $alternativeCredential) {
            if(!isset($alternativeCredential->type)
                || !isset($alternativeCredential->type->id)
                || $alternativeCredential->type->id != $alternativeCredentialType->id) {
                continue;
            }
            return $alternativeCredential->value;
        }

        return '';
    }

    private function hasAlternativeCredentialOfType($peron, $alternativeCredentialType) : bool {
        $value = $this->getAlternativeCredentialOfType($peron, $alternativeCredentialType);

        return $value != '';
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
        $programmes = $this->curriculumLookupService->getActiveProgrammes();

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

            $course = $this->courseService->updateOrCreateCourse($data);
            $this->trace->output("Processed: $programme->courseCode");
        }

        $this->trace->output("...Finished creating psuedo courses");
        $this->trace->finished();
    }

    private function getMoodleCourseIdNumber($guid, $academicLevel) {
        return "$academicLevel--$guid";
    }

    private function getApiKey() {
        $apiKey = get_config('enrol_ethos', 'ethosapikey');

        if (!$apiKey) {
            throw new \Exception('Ethos API key not set');
        }

        return $apiKey;
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
            $time = $time_end - $time_end;
            $this->trace->output("Filled user profile in $time seconds");

            $time_start = microtime(true);
            $this->userService->addDefaultEnrolments($user);
            $time_end = microtime(true);
            $time = $time_end - $time_end;
            $this->trace->output("Added default enrolments in $time seconds");

            $time_start = microtime(true);
            $this->userService->updateUserProfile($user);
            $time_end = microtime(true);
            $time = $time_end - $time_end;
            $this->trace->output("Updated user profile in $time seconds");

        } else {
            $this->trace->output("No ethos user found");
        }
    }

    private function getEthosStudent($user) {
        if (isset($user->userProfile->bannerGuid) && $user->userProfile->bannerGuid) {
            $this->trace->output("Using GUID: '{$user->userProfile->bannerGuid}'");
            return $this->studentLookupService->lookupStudentFromPersonId($user->userProfile->bannerGuid);
        } elseif (isset($user->username)) {
            $this->trace->output("Using Username: '{$user->username}'");
            return $this->studentLookupService->lookupStudentFromBannerId($user->username);
        }
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
