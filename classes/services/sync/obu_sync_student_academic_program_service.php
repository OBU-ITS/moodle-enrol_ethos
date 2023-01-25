<?php
namespace enrol_ethos\services\sync;

use enrol_ethos\entities\obu_user_enrolments_info;
use enrol_ethos\ethosclient\providers\ethos_student_academic_program_provider;
use enrol_ethos\services\enrolments\obu_program_enrolment_service;
use enrol_ethos\services\moodle\mdl_user_enrol_service;
use progress_trace;

class obu_sync_student_academic_program_service
{
    private ethos_student_academic_program_provider $studentAcademicProgramProvider;
    private obu_program_enrolment_service $programEnrolmentService;
    private mdl_user_enrol_service $userEnrolService;

    private function __construct()
    {
        $this->studentAcademicProgramProvider = ethos_student_academic_program_provider::getInstance();
        $this->programEnrolmentService = obu_program_enrolment_service::getInstance();
        $this->userEnrolService = mdl_user_enrol_service::getInstance();
    }

    private static ?obu_sync_student_academic_program_service $instance = null;
    public static function getInstance(): obu_sync_student_academic_program_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sync(progress_trace $trace, string $id)
    {
        $ethosStudentAcademicProgram = $this->studentAcademicProgramProvider->get($id);

        if($ethosStudentAcademicProgram) {
            $enrolments = new obu_user_enrolments_info();
            $this->programEnrolmentService->get($enrolments, $ethosStudentAcademicProgram);
            $this->userEnrolService->handleEnrolment($trace, $enrolments);
        }
        else {
            $this->userEnrolService->removeEnrolment($trace, $id);
        }
    }
}