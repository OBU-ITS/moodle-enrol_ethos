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

    /**
     * Re-sync user
     *
     * @param progress_trace $trace
     * @param string $username
     * @return void
     */
    public function reSyncUser(progress_trace $trace, string $username) : bool {
        $user = $this->userRepo->getByUsername($username);
        if($user == null) {
            $trace->output("User with username ($username) not found.");
            return false;
        }
        if($user->getCustomData()->personGuid == '') {
            $trace->output("User with username ($username) does not have a person guid.");
            return false;
        }

        // TODO : COmplete resync Persons
//        $oldHolds = $user->getCustomData()->personHolds;
//        $trace->output("Existing holds:");
//        $trace->output("$oldHolds");

//        $user->getCustomData()->personHolds = '';
//
//        $holds = $this->personHoldProvider->getByPersonGuid($user->getCustomData()->personGuid);
//        $trace->output("Holds from Ethos:");
//        $trace->output(json_encode($holds));
//
//        array_map(function ($hold) use ($user, &$updated) {
//            $this->personHoldService->update($hold, $user);
//        }, $holds);

//        if(strcasecmp($oldHolds, $user->getCustomData()->personHolds) != 0) {
//            $trace->output("New holds:");
//            $trace->output($user->getCustomData()->personHolds);
//            $this->saveUser($trace, $user);
//        }
//        else {
//            $trace->output("No changes required for user.");
//        }

        return true;
    }

    public function sync(progress_trace $trace, $id)
    {
        $ethosPerson = $this->personProvider->get($id);
        if($ethosPerson == null) {
            $trace->output("Person ($id) not found to update.");
            return;
        }

        $users = new obu_users_info();
        if($this->alternativeCredentialService->hasAlternativeCredentialOfType($ethosPerson, $this->employeeAlternativeCredentialType)) {
            $trace->output("Start upsert for staff id:" . $id);
            $this->staffService->get($users, $ethosPerson);
        }
        else {
            $moodleUser = $this->userService->getUserByPersonGuid($id);
            if($moodleUser) {
                $trace->output("Start update for student id:" . $id);
                $this->studentService->get($users, $ethosPerson);
            }
            else {
                $trace->output("Skip insert for student id:" . $id);
            }
        }

        $this->userService->handleUserCreation($trace, $users);
    }

    public function syncByUser(progress_trace $trace, mdl_user $user)
    {
        $customData = $user->getCustomData();
        $trace->output("Start re-sync for person id:" . $customData->personGuid);

        $ethosPerson = $this->personProvider->get($customData->personGuid);
        if($ethosPerson == null) {
            $trace->output("Person ($customData->personGuid) not found to update.");
            return;
        }

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