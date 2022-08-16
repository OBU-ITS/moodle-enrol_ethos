<?php
namespace enrol_ethos\services\sync;

use enrol_ethos\entities\obu_users_info;
use enrol_ethos\ethosclient\providers\ethos_student_advisor_relationship_provider;
use enrol_ethos\services\moodle\mdl_user_service;
use enrol_ethos\services\obu_staff_service;
use enrol_ethos\services\obu_student_service;
use progress_trace;

class obu_sync_student_advisor_service
{
    private ethos_student_advisor_relationship_provider $studentAdvisorProvider;
    private obu_staff_service $staffService;
    private obu_student_service $studentService;
    private mdl_user_service $userService;

    private function __construct()
    {
        $this->studentAdvisorProvider = ethos_student_advisor_relationship_provider::getInstance();
        $this->staffService = obu_staff_service::getInstance();
        $this->studentService = obu_student_service::getInstance();
        $this->userService = mdl_user_service::getInstance();
    }

    private static ?obu_sync_student_advisor_service $instance = null;
    public static function getInstance(): obu_sync_student_advisor_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sync(progress_trace $trace, $id)
    {
        $ethosStudentAdvisor = $this->studentAdvisorProvider->get($id);

        $users = new obu_users_info();
        $this->staffService->get($users, $ethosStudentAdvisor->getAdvisor());
        $this->studentService->get($users, $ethosStudentAdvisor->getStudent());

        $this->userService->handleUserCreation($trace, $users);
    }
}