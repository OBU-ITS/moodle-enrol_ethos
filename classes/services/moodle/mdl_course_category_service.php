<?php
namespace enrol_ethos\services\moodle;

use core_course_category;
use enrol_ethos\entities\obu_course_category_info;
use enrol_ethos\repositories\db_course_category_repository;
use progress_trace;
use stdClass;

class mdl_course_category_service
{
    private db_course_category_repository $courseCategoryRepo;

    private function __construct()
    {
        global $DB;

        $this->courseCategoryRepo = new db_course_category_repository($DB);
    }

    private static ?mdl_course_category_service $instance = null;
    public static function getInstance(): mdl_course_category_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function upsertCourseCategory(progress_trace $trace, obu_course_category_info $category, string $categoryIdNumber, ?int $parentCategoryId) : int {
        if($category->id == 0) {
            return 0;
        }

        if($courseCategory = $this->courseCategoryRepo->get($categoryIdNumber))
        {
            if($updatedCourseCategory = $this->getUpdatedCategory($courseCategory, $category, $categoryIdNumber, $parentCategoryId))
            {
                $courseCategory->update($updatedCourseCategory);
                $trace->output("Course category updated : $categoryIdNumber");
            }
        }
        else {
            $courseCategory = $this->courseCategoryRepo->create($category, $categoryIdNumber, $parentCategoryId);
            $trace->output("Course category created : $categoryIdNumber");
        }

        return $courseCategory->id;
    }

    private function getUpdatedCategory(core_course_category $courseCategory, obu_course_category_info $category, string $categoryIdNumber, int $parentId) {
        $hasChanges = false;
        $updatedCategory = new stdClass();

        if(strval($courseCategory->name) !== $category->name) {
            $updatedCategory->name = $category->name;
            $hasChanges = true;
        }

        if(strval($courseCategory->parent) !== strval($parentId)) {
            $updatedCategory->parent = $parentId;
            $hasChanges = true;
        }

        if(strval($courseCategory->idnumber) !== $categoryIdNumber) {
            $updatedCategory->idnumber = $categoryIdNumber;
            $hasChanges = true;
        }

        if($hasChanges) {
            return $updatedCategory;
        }

        return false;
    }

    public function getCategoryId(string $keyPrefix, string $codeName) : string {
        if($keyPrefix == '') {
            return $codeName;
        }

        return $keyPrefix . '~' . $codeName;
    }

    public function getCategoryPrefix(string $keyPrefix, string $codeName, string $alternateCodeName) : string {
        if($codeName == '0') {
            return '';
        }

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