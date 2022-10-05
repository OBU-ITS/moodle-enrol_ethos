<?php
namespace enrol_ethos\services\sync;

use enrol_ethos\entities\mdl_user;
use enrol_ethos\entities\obu_users_info;
use enrol_ethos\ethosclient\providers\ethos_person_hold_provider;
use enrol_ethos\repositories\db_user_repository;
use enrol_ethos\services\moodle\mdl_user_service;
use enrol_ethos\services\obu_person_hold_service;
use progress_trace;

class obu_sync_person_hold_service
{
    private ethos_person_hold_provider $personHoldProvider;
    private obu_person_hold_service $personHoldService;
    private mdl_user_service $userService;
    private db_user_repository $userRepo;

    private function __construct()
    {
        global $DB;

        $this->personHoldProvider = ethos_person_hold_provider::getInstance();
        $this->personHoldService = obu_person_hold_service::getInstance();
        $this->userService = mdl_user_service::getInstance();
        $this->userRepo = new db_user_repository($DB);
    }

    private static ?obu_sync_person_hold_service $instance = null;
    public static function getInstance(): obu_sync_person_hold_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     *
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
        $oldHolds = $user->getCustomData()->personHolds;
        $user->getCustomData()->personHolds = '';

        $holds = $this->personHoldProvider->getByPersonGuid($user->getCustomData()->personGuid);
        array_map(function ($hold) use ($user, &$updated) {
            $this->personHoldService->update($hold, $user);
        }, $holds);

        if(strcasecmp($oldHolds, $user->getCustomData()->personHolds) != 0) {
            $this->saveUser($trace, $user);
        }

        return true;
    }

    /**
     * Synchronise a users person holds
     *
     * @param progress_trace $trace
     * @param string $id Person Hold Guid
     */
    public function sync(progress_trace $trace, string $id) {
        $hold = $this->personHoldProvider->get($id);
        if($hold == null) {
            $trace->output("Hold ($id) not found to update.");
            return;
        }

        $user = $this->userRepo->getUserWhereProfileFieldEquals("person_guid", $hold->getPersonId());
        if($user == null) {
            $trace->output("User ({$hold->getPersonId()}) with hold ($id) not found.");
            return;
        }

        $trace->output("Updating user ($user->email) with hold ($id)");
        if($this->personHoldService->update($hold, $user)) {
            $this->saveUser($trace, $user);
        }
    }

    /**
     * Remove person hold from user
     *
     * @param progress_trace $trace
     * @param string $id Person Hold Guid
     */
    public function remove(progress_trace $trace, string $id) {
        $user = $this->userRepo->getUserWhereProfileFieldContains("person_holds", $id);
        if($user == null) {
            $trace->output("User with hold ($id) not found.");
            return;
        }

        $this->personHoldService->remove($id, $user);
        $this->saveUser($trace, $user);
    }

    /**
     * Save User changes
     *
     * @param progress_trace $trace
     * @param mdl_user $user
     */
    private function saveUser(progress_trace $trace, mdl_user $user) {
        $users = new obu_users_info();
        $users->addUser($user);
        $this->userService->handleUserCreation($trace, $users);
    }
}