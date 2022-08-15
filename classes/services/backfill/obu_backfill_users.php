<?php
namespace enrol_ethos\services\moodle;

use enrol_ethos\services\sync\obu_sync_person_service;
use progress_trace;

class obu_backfill_users
{
    private const RUN_LIMIT = 500;

    private mdl_user_service $userService;
    private obu_sync_person_service $syncPersonService;

    private function __construct()
    {
        $this->userService = mdl_user_service::getInstance();
        $this->syncPersonService = obu_sync_person_service::getInstance();
    }

    private static ?obu_backfill_users $instance = null;
    public static function getInstance(): obu_backfill_users
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function backfill(progress_trace $trace, $max = 0) {
        $offset = 0;
        $totalResults = 0;

        do {
            $limit = ($max && ($max < ($totalResults + self::RUN_LIMIT))) ? ($max - $totalResults) : self::RUN_LIMIT;

            $users = $this->userService->getLdapUsers($limit, $offset);
            foreach($users as $user) {
                $this->syncPersonService->syncByUser($trace, $user);
            }


            $offset += self::RUN_LIMIT;
            $resultsCount = count($users);
            $totalResults += $resultsCount;
        }
        while($resultsCount > 0 && ($max == 0 || ($max > $totalResults)));
    }
}