<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_course;
use enrol_ethos\entities\obu_course_hierarchy_info;

class mdl_course_service
{
    private obu_module_run_service $moduleRunService;
    private mdl_course_category_service $courseCategoryService;

    private function __construct()
    {
        $this->moduleRunService = obu_module_run_service::getInstance();
        $this->courseCategoryService = mdl_course_category_service::getInstance();
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

        $this->handleCourseCreation($courseHierarchy);
    }

    public function reSyncAllCourses() {
        $courseHierarchy = $this->moduleRunService->getAll();

        $this->handleCourseCreation($courseHierarchy);
    }

    private function handleCourseCreation(obu_course_hierarchy_info $courseHierarchy, string $keyPrefix = '') {
        $categoryId = $this->getCategoryId($keyPrefix, $courseHierarchy->currentCategory->codeName, $courseHierarchy->currentCategory->alternateCodeName);
        $this->courseCategoryService->ensureCourseCategory($courseHierarchy->currentCategory, $categoryId);

        $childrenCategories = $courseHierarchy->getSubCategories();
        foreach($childrenCategories as $childCategory) {
            $this->handleCourseCreation($childCategory, $categoryId);
        }

        $childrenCourses = $courseHierarchy->getCourses();
        foreach($childrenCourses as $childCourse) {
            $this->upsertCourse($childCourse);
        }
    }

    private function upsertCourse(mdl_course $course) {
        // TODO
    }

    private function getCategoryId(string $keyPrefix, string $codeName, string $alternateCodeName) : string {
        if($keyPrefix == '') {
            return $alternateCodeName == ""
                ? $codeName
                : $alternateCodeName;
        }

        return $alternateCodeName == ""
            ? $keyPrefix . '~' . $codeName
            : $keyPrefix . '~' . $alternateCodeName;
    }
}