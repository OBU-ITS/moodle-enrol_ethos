<?php
namespace enrol_ethos\services\sync;

use enrol_ethos\ethosclient\providers\ethos_person_hold_provider;
use enrol_ethos\repositories\db_user_repository;
use obu_person_hold_service;
use progress_trace;

class obu_sync_person_hold_service
{
    private ethos_person_hold_provider $personHoldProvider;
    private obu_person_hold_service $personHoldService;
    private db_user_repository $userRepo;

    private function __construct()
    {
        global $DB;

        $this->personHoldProvider = ethos_person_hold_provider::getInstance();
        $this->personHoldService = obu_person_hold_service::getInstance();
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

    public function sync(progress_trace $trace, string $id) {
        $hold = $this->personHoldProvider->get($id);
        if($hold == null) {
            $trace->output("Hold ($id) not found to update.");
            return;
        }

        $user = $this->userRepo->getUserWhereProfileFieldEquals("person_guid", $hold->getPersonId());
        if($user == null) {
            $trace->output("User with hold ($id) not found.");
            return;
        }

        $this->personHoldService->update($hold, $user);
    }

    public function remove(progress_trace $trace, string $id) {
        $user = $this->userRepo->getUserWhereProfileFieldContains("person_holds", $id);
        if($user == null) {
            $trace->output("User with hold ($id) not found.");
            return;
        }

        $this->personHoldService->remove($id, $user);
    }
}