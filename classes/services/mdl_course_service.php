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

    public function reSyncModuleRun($id) {
        $hierarchy = obu_course_hierarchy_info::getTopCategory();

        echo "Start resync module run for id:" . $id . "<br />";
        $this->moduleRunService->get($hierarchy, $id);

        $this->handleCourseCreation($hierarchy);
    }

    public function reSyncAllModuleRuns() {
        $hierarchy = obu_course_hierarchy_info::getTopCategory();

        $this->moduleRunService->getAll($hierarchy);

        $this->handleCourseCreation($hierarchy);
    }

    private function handleCourseCreation(obu_course_hierarchy_info $courseHierarchy, string $keyPrefix = '') {
        $categoryId = $this->courseCategoryService->getCategoryId($keyPrefix, $courseHierarchy->currentCategory->codeName);
        $this->courseCategoryService->ensureCourseCategory($courseHierarchy->currentCategory, $categoryId);

        if($courseHierarchy->hasSubCategories()) {
            $childrenCategories = $courseHierarchy->getSubCategories();
            $childKeyPrefix = $this->courseCategoryService->getCategoryPrefix($keyPrefix, $courseHierarchy->currentCategory->codeName, $courseHierarchy->currentCategory->alternateCodeName);
            foreach ($childrenCategories as $childCategory) {
                $this->handleCourseCreation($childCategory, $childKeyPrefix);
            }
        }

        $childrenCourses = $courseHierarchy->getCourses();
        foreach($childrenCourses as $childCourse) {
            $childCourse->catid = $categoryId;
            $this->upsertCourse($childCourse);
        }
    }

    private function upsertCourse(mdl_course $course) {
        // TODO
        echo "<br/>Upsert course <br/>";
        echo "Id Number : " . $course->idnumber . " <br/>";
        echo "Shortname : " . $course->shortname . " <br/>";
        echo "Full Name : " . $course->name . " <br/>";
        echo "Cat Id : " . $course->catid . " <br/>";
    }


}