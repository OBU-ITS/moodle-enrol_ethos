<?php
namespace enrol_ethos\services\sync;

use enrol_ethos\entities\obu_user_enrolments_info;
use enrol_ethos\ethosclient\providers\ethos_section_registration_provider;
use enrol_ethos\services\enrolments\obu_module_run_enrolment_service;
use enrol_ethos\services\enrolments\obu_subject_group_enrolment_service;
use enrol_ethos\services\moodle\mdl_user_enrol_service;
use progress_trace;

class obu_sync_section_registration_service
{
    private ethos_section_registration_provider $sectionRegistrationProvider;
    private obu_module_run_enrolment_service $moduleRunEnrolmentService;
    private obu_subject_group_enrolment_service $subjectGroupEnrolmentService;
    private mdl_user_enrol_service $userEnrolService;

    private function __construct()
    {
        $this->sectionRegistrationProvider = ethos_section_registration_provider::getInstance();
        $this->moduleRunEnrolmentService = obu_module_run_enrolment_service::getInstance();
        $this->subjectGroupEnrolmentService = obu_subject_group_enrolment_service::getInstance();
        $this->userEnrolService = mdl_user_enrol_service::getInstance();
    }

    private static ?obu_sync_section_registration_service $instance = null;
    public static function getInstance(): obu_sync_section_registration_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sync(progress_trace $trace, string $id)
    {
        $ethosSectionRegistration = $this->sectionRegistrationProvider->get($id);

        if($ethosSectionRegistration) {
            $enrolments = new obu_user_enrolments_info();
            $this->moduleRunEnrolmentService->get($enrolments, $ethosSectionRegistration);
            $this->subjectGroupEnrolmentService->get($enrolments, $ethosSectionRegistration);
            $this->userEnrolService->handleEnrolment($trace, $enrolments);
        }
        else {
            $this->userEnrolService->removeEnrolment($trace, $id);
        }
    }
}