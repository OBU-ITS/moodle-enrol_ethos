<?php
namespace enrol_ethos\services\sync;

use enrol_ethos\entities\mdl_user;
use enrol_ethos\entities\obu_users_info;
use enrol_ethos\ethosclient\entities\ethos_alternative_credential_type_info;
use enrol_ethos\ethosclient\providers\ethos_person_provider;
use enrol_ethos\services\moodle\mdl_user_service;
use enrol_ethos\services\obu_alternative_credential_service;
use enrol_ethos\services\obu_staff_service;
use enrol_ethos\services\obu_student_service;
use progress_trace;

class obu_sync_person_service
{
    private const RUN_LIMIT = 100;

    private obu_staff_service $staffService;
    private obu_student_service $studentService;
    private mdl_user_service $userService;
    private ethos_person_provider $personProvider;
    private obu_alternative_credential_service $alternativeCredentialService;

    private ethos_alternative_credential_type_info $employeeAlternativeCredentialType;

    private function __construct()
    {
        $this->staffService = obu_staff_service::getInstance();
        $this->studentService = obu_student_service::getInstance();
        $this->userService = mdl_user_service::getInstance();
        $this->personProvider = ethos_person_provider::getInstance();
        $this->alternativeCredentialService = obu_alternative_credential_service::getInstance();

        $this->employeeAlternativeCredentialType = $this->alternativeCredentialService->getEmployeeNumberAlternativeCredentialType();
    }

    private static ?obu_sync_person_service $instance = null;
    public static function getInstance(): obu_sync_person_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sync(progress_trace $trace, $id)
    {
        $trace->output("Start re-sync for person id:" . $id);

        $ethosPerson = $this->personProvider->get($id);

        $users = new obu_users_info();
        if($this->alternativeCredentialService->hasAlternativeCredentialOfType($ethosPerson, $this->employeeAlternativeCredentialType)) {
            $this->staffService->get($users, $ethosPerson);
        }
        else {
            $moodleUser = $this->userService->getUserByPersonGuid($id);
            if($moodleUser) {
                $this->studentService->get($users, $ethosPerson);
            }
        }

        $this->userService->handleUserCreation($trace, $users);
    }

    public function syncByUser(progress_trace $trace, mdl_user $user)
    {
        $customData = $user->getCustomData();
        $trace->output("Start re-sync for person id:" . $customData->personGuid);

        $ethosPerson = $this->personProvider->get($customData->personGuid);

        $users = new obu_users_info();
        if($this->alternativeCredentialService->hasAlternativeCredentialOfType($ethosPerson, $this->employeeAlternativeCredentialType)) {
            $this->staffService->get($users, $ethosPerson);
        }
        else {
            $this->studentService->get($users, $ethosPerson);
        }

        $this->userService->handleUserCreation($trace, $users);
    }

//    public function syncAll(progress_trace $trace, int $max = 0) {
//        $offset = 0;
//        $totalResults = 0;
//
//        do {
//            $users = new obu_users_info();
//
//            $limit = ($max && ($max < ($totalResults + self::RUN_LIMIT))) ? ($max - $totalResults) : self::RUN_LIMIT;
//            $resultsCount = $this->staffService->getBatch($users, $limit, $offset);
//
//            $this->userService->handleUserCreation($trace, $users);
//
//            $offset += self::RUN_LIMIT;
//            $totalResults += $resultsCount;
//        }
//        while($resultsCount > 0 && ($max == 0 || ($max > $totalResults)));
//    }
}