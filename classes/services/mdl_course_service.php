<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\obu_course_hierarchy_info;

class mdl_course_service
{
    private obu_module_run_service $moduleRunService;

    private function __construct()
    {
        $this->moduleRunService = obu_module_run_service::getInstance();
    }

    private static ?mdl_course_service $instance = null;
    public static function getInstance(): mdl_course_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function reSyncCourse($id) {
        $courseHierarchy = $this->moduleRunService->get($id);

        $this->handlingCourseCreation($courseHierarchy);
    }

    public function reSyncAllCourses() {
        $courseHierarchy = $this->moduleRunService->getAll();

        $this->handlingCourseCreation($courseHierarchy);
    }

    private function handlingCourseCreation(obu_course_hierarchy_info $courseHierarchy) {
        // go through course hierarchy,

        // ensure course_categories
        //  mdl_course_category_service->ensureCourseCategory()

        // create / update courses
    }
}