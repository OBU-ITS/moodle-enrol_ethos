<?php

namespace enrol_ethos\services;

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
        $this->trace->output('Processing all users...');

        $users = $this->userService->getAllUsers();
        $this->process_users($users);
        
        $this->trace->output('...finished processing all users');
        $this->trace->finished();
    }
    
    public function process_ethos_updates() {
        //Get list of users to work with.
        
        $this->trace->output("Reading messages from ethos queue...");
        $studentsWithChanges = $this->studentLookupService->getStudentsWithChanges();

        $count = count($studentsWithChanges);
        $this->trace->output("Found $count students with changes");

        //TODO: Check unique

        $users = array();

        if ($studentsWithChanges) {
            $users = $this->userService->getUserProfilesWithBannerIds($studentsWithChanges);
            $count = count($users);
            $this->trace->output("Found $count moodle users that matched");
        }

        //Loop through each and fill in the missing profile fields
        foreach ($users as $user) {
            if (!$user->userProfile->bannerGuid) {
                continue;
            }        
            process_user($user);
        } 

        $this->trace->output("...Finished reading messages from ethos queue");
        $this->trace->finished();
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
            
            $data = [
            'idnumber' => $programme->guid,
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

        $this->trace->output("Processing user: $user->username");

        if ($student = $this->getEthosStudent($user)) {

            $this->trace->output("Found Ethos user - Banner GUID: $student->personId");

            $this->fill_user_profile($student, $user);
            $this->userService->addDefaultEnrolments($user);
            $this->userService->updateUserProfile($user);
        } else {
            $this->trace->output("No ethos user found");
        }
    }


    private function getEthosStudent($user) {
        if (isset($user->userProfile->bannerGuid)) {
            return $this->studentLookupService->lookupStudentFromPersonId($user->userProfile->bannerGuid);
        } elseif (isset($user->username)) {
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