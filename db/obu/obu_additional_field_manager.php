<?php

namespace enrol_ethos\managers;

use core_course\customfield\course_handler;
use enrol_ethos\repositories\db_profile_category_repository;
use enrol_ethos\repositories\db_profile_field_repository;
use enrol_ethos\services\moodle\mdl_course_custom_field_service;
use enrol_ethos\services\profile_field_service;

class obu_additional_field_manager
{
    private mdl_course_custom_field_service $courseCustomFieldService;
    private profile_field_service $profileFieldService;

    private function __construct()
    {
        global $DB;

        $this->courseCustomFieldService = mdl_course_custom_field_service::getInstance();

        $profileFieldRepository = new db_profile_field_repository($DB);
        $profileCategoryRepository = new db_profile_category_repository($DB);
        $this->profileFieldService = new profile_field_service($profileFieldRepository, $profileCategoryRepository);
    }

    private static ?obu_additional_field_manager $instance = null;
    public static function getInstance() : obu_additional_field_manager
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function ensureAdditionalFields() {
        $this->ensureCourseFields();
        $this->ensureUserFields();
    }

    private function ensureCourseFields() {
        $visibleCourseCategory = $this->courseCustomFieldService->ensureCustomFieldCategory("Banner Data");
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Test field", "test_field_1", PARAM_TEXT, 50, 200, course_handler::VISIBLETOTEACHERS);
        // TODO : Update example above and complete the course field definitions

        $hiddenCourseCategory = $this->courseCustomFieldService->ensureCustomFieldCategory("Banner Data (Hidden)");
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Test field", "test_field_text", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Test field", "test_field_date", \customfield_date\field_controller::TYPE, null, null, course_handler::NOTVISIBLE);
        // TODO : Update example above and complete the course field definitions
    }

    private function ensureUserFields() {
        // TODO : Revise this
        $this->profileFieldService->addDefaultCategory();
        $this->profileFieldService->addDefaultFields();
    }
}