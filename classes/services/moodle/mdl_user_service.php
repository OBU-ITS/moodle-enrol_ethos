<?php
namespace enrol_ethos\services\moodle;

use enrol_ethos\entities\mdl_user_profile;

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
}