<?php
namespace enrol_ethos\services\moodle;

use enrol_ethos\entities\mdl_user_enrolment;
use enrol_ethos\entities\obu_user_enrolments_info;
use progress_trace;

class mdl_user_enrol_service
{

    private function __construct()
    {
    }

    private static ?mdl_user_enrol_service $instance = null;
    public static function getInstance(): mdl_user_enrol_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function handleEnrolment(progress_trace $trace, obu_user_enrolments_info $enrolments) {
        // TODO : Joe
    }

    public function removeEnrolment(progress_trace $trace, string $id) {
        // TODO : Joe
    }
}