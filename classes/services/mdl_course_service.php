<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_course;
use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\repositories\db_course_repository;
use stdClass;

class mdl_course_service
{
    private obu_module_run_service $moduleRunService;
    private mdl_course_category_service $courseCategoryService;
    private db_course_repository $courseRepo;

    private function __construct()
    {
        global $DB;

        $this->moduleRunService = obu_module_run_service::getInstance();
        $this->courseCategoryService = mdl_course_category_service::getInstance();
        $this->courseRepo = new db_course_repository($DB);
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

    private function handleCourseCreation(obu_course_hierarchy_info $courseHierarchy, string $keyPrefix = '', ?int $parentId = null) {
        $categoryIdNumber = $this->courseCategoryService->getCategoryId($keyPrefix, $courseHierarchy->currentCategory->codeName);
        $categoryId = $this->courseCategoryService->upsertCourseCategory($courseHierarchy->currentCategory, $categoryIdNumber, $parentId);

        if($courseHierarchy->hasSubCategories()) {
            $childrenCategories = $courseHierarchy->getSubCategories();
            $childKeyPrefix = $this->courseCategoryService->getCategoryPrefix($keyPrefix, $courseHierarchy->currentCategory->codeName, $courseHierarchy->currentCategory->alternateCodeName);
            foreach ($childrenCategories as $childCategory) {
                $this->handleCourseCreation($childCategory, $childKeyPrefix, $categoryId);
            }
        }

        $childrenCourses = $courseHierarchy->getCourses();
        foreach($childrenCourses as $childCourse) {
            $childCourse->catid = $categoryId;
            $this->upsertCourse($childCourse);
        }
    }

    private function upsertCourse(mdl_course $data) {
        if($course = $this->courseRepo->findOne($data->idnumber))
        {
            if($updatedCourse = $this->getUpdatedCourse($course, $data))
            {
                $this->courseRepo->update($updatedCourse);
                echo "Course updated : $data->name <br/>";
            }
            else
            {
                echo "Course found : $data->name <br/>";
            }
        }
        else {
            echo "Course created : $data->name <br/>";
            $this->courseRepo->create($data);
        }
    }

    private function getUpdatedCourse(mdl_course $currentCourse, mdl_course $newCourse) {
        $hasChanges = false;

        if(strval($currentCourse->idnumber) !== $newCourse->idnumber) {
            $currentCourse->idnumber = $newCourse->idnumber;
            $hasChanges = true;
        }

        if(strval($currentCourse->shortname) !== $newCourse->shortname) {
            $currentCourse->shortname = $newCourse->shortname;
            $hasChanges = true;
        }

        if(strval($currentCourse->name) !== $newCourse->name) {
            $currentCourse->name = $newCourse->name;
            $hasChanges = true;
        }

        if(strval($currentCourse->catid) !== strval($newCourse->catid)) {
            $currentCourse->catid = $newCourse->catid;
            $hasChanges = true;
        }

        if(strval($currentCourse->startdate) !== strval($newCourse->startdate)) {
            $currentCourse->startdate = $newCourse->startdate;
            $hasChanges = true;
        }

        if(strval($currentCourse->enddate) !== strval($newCourse->enddate)) {
            $currentCourse->enddate = $newCourse->enddate;
            $hasChanges = true;
        }

        if($hasChanges) {
            return $currentCourse;
        }

        return false;
    }
}