<?php

namespace enrol_ethos\services\sync;

use enrol_ethos\entities\obu_users_info;
use enrol_ethos\ethosclient\providers\ethos_section_instructors_provider;
use enrol_ethos\services\moodle\mdl_user_service;
use enrol_ethos\services\obu_staff_service;
use progress_trace;

class obu_sync_section_instructor_service
{
    private ethos_section_instructors_provider $sectionInstructorProvider;
    private obu_staff_service $staffService;
    private mdl_user_service $userService;

    private function __construct()
    {
        $this->sectionInstructorProvider = ethos_section_instructors_provider::getInstance();
        $this->staffService = obu_staff_service::getInstance();
        $this->userService = mdl_user_service::getInstance();
    }

    private static ?obu_sync_section_instructor_service $instance = null;
    public static function getInstance(): obu_sync_section_instructor_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sync(progress_trace $trace, $id)
    {
        $ethosSectionInstructor = $this->sectionInstructorProvider->get($id);

        $users = new obu_users_info();
        $this->staffService->get($users, $ethosSectionInstructor->getInstructor());

        $this->userService->handleUserCreation($trace, $users);
    }
}