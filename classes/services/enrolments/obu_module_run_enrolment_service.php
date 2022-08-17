<?php
namespace enrol_ethos\services\enrolments;

use enrol_ethos\entities\obu_user_enrolments_info;
use enrol_ethos\ethosclient\entities\ethos_section_registration_info;

class obu_module_run_enrolment_service {
    private function __construct()
    {
    }

    private static ?obu_module_run_enrolment_service $instance = null;
    public static function getInstance() : obu_module_run_enrolment_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(obu_user_enrolments_info $enrolments, ethos_section_registration_info $sectionRegistration) {
        // TODO : Joe
    }
}