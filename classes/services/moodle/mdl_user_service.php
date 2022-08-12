<?php
namespace enrol_ethos\services\moodle;

use enrol_ethos\entities\mdl_user_profile;
use enrol_ethos\entities\obu_users_info;
use progress_trace;

class mdl_user_service
{
    private function __construct()
    {
    }

    private static ?mdl_user_service $instance = null;
    public static function getInstance(): mdl_user_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getCustomData(int $id) : mdl_user_profile {
        return new mdl_user_profile(); // TODO
    }

    public function handleUserCreation(progress_trace $trace, obu_users_info $users) {
        // TODO : Joe
    }
}