<?php
namespace enrol_ethos\services\enrolments;

class obu_special_enrolment_service {
    private function __construct()
    {
    }

    private static ?obu_special_enrolment_service $instance = null;
    public static function getInstance() : obu_special_enrolment_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }
}