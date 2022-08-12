<?php
namespace enrol_ethos\services\sync;

use enrol_ethos\entities\obu_users_info;
use enrol_ethos\services\moodle\mdl_user_service;
use enrol_ethos\services\obu_staff_service;
use progress_trace;

class obu_sync_person_service
{
    private const RUN_LIMIT = 100;

    private obu_staff_service $staffService;
    private mdl_user_service $userService;

    private function __construct()
    {
        $this->staffService = obu_staff_service::getInstance();
        $this->userService = mdl_user_service::getInstance();
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
        $users = new obu_users_info();

        $trace->output("Start re-sync staff for id:" . $id);

        $this->staffService->get($users, $id);

        $this->userService->handleUserCreation($trace, $users);
    }


    public function syncAll(progress_trace $trace, int $max = 0) {
        $offset = 0;
        $totalResults = 0;

        do {
            $users = new obu_users_info();

            $limit = ($max && ($max < ($totalResults + self::RUN_LIMIT))) ? ($max - $totalResults) : self::RUN_LIMIT;
            $resultsCount = $this->staffService->getBatch($users, $limit, $offset);

            $this->userService->handleUserCreation($trace, $users);

            $offset += self::RUN_LIMIT;
            $totalResults += $resultsCount;
        }
        while($resultsCount > 0 && ($max == 0 || ($max > $totalResults)));
    }
}