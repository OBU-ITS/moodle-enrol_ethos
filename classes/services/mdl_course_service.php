<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_course;
use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\repositories\db_course_repository;
use progress_trace;

class mdl_course_service
{
    private const RUN_LIMIT = 100;

    private obu_module_run_service $moduleRunService;
    private obu_program_service $programService;
    private mdl_course_category_service $courseCategoryService;
    private db_course_repository $courseRepo;

    private function __construct()
    {
        global $DB;

        $this->moduleRunService = obu_module_run_service::getInstance();
        $this->programService = obu_program_service::getInstance();
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

    public function reSyncModuleRun(progress_trace $trace, $id) {
        $hierarchy = obu_course_hierarchy_info::getTopCategory();

        $trace->output("Start re-sync module run for id:" . $id);

        $this->moduleRunService->get($hierarchy, $id);

        $this->handleCourseCreation($trace, $hierarchy);
    }

    public function reSyncProgram(progress_trace $trace, $id) {
        $hierarchy = obu_course_hierarchy_info::getTopCategory();

        $trace->output("Start re-sync program for id:" . $id);

        $this->programService->get($hierarchy, $id);

        $this->handleCourseCreation($trace, $hierarchy);
    }

    public function reSyncAllModuleRuns(progress_trace $trace, int $max = 0) {
        $offset = 0;
        $totalResults = 0;

        do {
            $hierarchy = obu_course_hierarchy_info::getTopCategory();

            $limit = ($max && ($max < ($totalResults + self::RUN_LIMIT))) ? ($max - $totalResults) : self::RUN_LIMIT;
            $resultsCount = $this->moduleRunService->getBatch($hierarchy, $limit, $offset);

            $this->handleCourseCreation($trace, $hierarchy);

            $offset += self::RUN_LIMIT;
            $totalResults += $resultsCount;
        }
        while($resultsCount > 0 && ($max == 0 || ($max > $totalResults)));
    }


    public function reSyncAllPrograms(progress_trace $trace, int $max = 0) {
        $offset = 0;
        $totalResults = 0;

        do {
            $hierarchy = obu_course_hierarchy_info::getTopCategory();

            $limit = ($max && ($max < ($totalResults + self::RUN_LIMIT))) ? ($max - $totalResults) : self::RUN_LIMIT;
            $resultsCount = $this->programService->getBatch($hierarchy, $limit, $offset);

            $this->handleCourseCreation($trace, $hierarchy);

            $offset += self::RUN_LIMIT;
            $totalResults += $resultsCount;
        }
        while($resultsCount > 0 && ($max == 0 || ($max > $totalResults)));
    }

    private function handleCourseCreation(progress_trace $trace, obu_course_hierarchy_info $courseHierarchy, string $keyPrefix = '', ?int $parentId = null) {
        $categoryIdNumber = $this->courseCategoryService->getCategoryId($keyPrefix, $courseHierarchy->currentCategory->codeName);
        $categoryId = $this->courseCategoryService->upsertCourseCategory($trace, $courseHierarchy->currentCategory, $categoryIdNumber, $parentId);

        if($courseHierarchy->hasSubCategories()) {
            $childKeyPrefix = $this->courseCategoryService->getCategoryPrefix($keyPrefix, $courseHierarchy->currentCategory->codeName, $courseHierarchy->currentCategory->alternateCodeName);

            $childrenCategories = $courseHierarchy->getSubCategories();
            foreach ($childrenCategories as $childCategory) {
                $this->handleCourseCreation($trace, $childCategory, $childKeyPrefix, $categoryId);
            }
        }

        $childrenCourses = $courseHierarchy->getCourses();
        foreach($childrenCourses as $childCourse) {
            $childCourse->catid = $categoryId;
            $this->upsertCourse($trace, $childCourse);
        }
    }

    private function upsertCourse(progress_trace $trace, mdl_course $data) {
        $course = $this->courseRepo->findOne($data->idnumber);
        if(!$course) {
            $course = $this->courseRepo->findOneByShortName($data->shortname);
        }

        if($course)
        {
            if($updatedCourse = $this->getUpdatedCourse($trace, $course, $data))
            {
                $this->courseRepo->update($updatedCourse);
                $trace->output("Course updated : $data->name ($data->idnumber) ($data->bannerId)");
            }
        }
        else {
            $this->courseRepo->create($data);
            $trace->output("Course created : $data->name ($data->id)");
        }
    }

    private function getUpdatedCourse(progress_trace $trace, mdl_course $currentCourse, mdl_course $newCourse) {
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
            $trace->output("$currentCourse->catid ! = $newCourse->catid");
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