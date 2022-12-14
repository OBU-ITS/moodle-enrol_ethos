<?php
namespace enrol_ethos\entities;

use core_course_category;

class obu_course_hierarchy_info {
    public obu_course_category_info $currentCategory;

    /**
     * @var obu_course_hierarchy_info[]
     */
    private array $subCategories;
    /**
     * @return obu_course_hierarchy_info[]
     */
    public function getSubCategories() : array {
        return $this->subCategories;
    }
    public function hasSubCategories() : bool {
        return (count($this->subCategories) > 0);
    }

    /**
     * @var mdl_course[]
     */
    private array $courses;
    /**
     * @return mdl_course[]
     */
    public function getCourses() : array {
        return $this->courses;
    }
    public function hasCourses() : bool {
        return (count($this->courses) > 0);
    }

    private function __construct(obu_course_category_info $category)
    {
        $this->currentCategory = $category;
        $this->subCategories = array();
        $this->courses = array();
    }

    public static function getTopCategory() : obu_course_hierarchy_info {
        $top = core_course_category::top();
        $topCategoryInfo = new obu_course_category_info('', '');
        $topCategoryInfo->id = $top->id;
        return new self($topCategoryInfo);
    }

    private function addSubCategory_internal(obu_course_hierarchy_info $category) {
        $this->subCategories[] = $category;
    }

    private function addCourse_internal(mdl_course $course) {
        $this->courses[] = $course;
    }

    /**
     * @param mdl_course $course
     * @param obu_course_category_info[] $courseCategories
     */
    public function addCourse(mdl_course $course, array $courseCategories) {
        $category = array_shift($courseCategories);

        if(isset($category)) {
            $key = $category->alternateCodeName == "" ? $category->codeName : $category->alternateCodeName;

            if(array_key_exists($key, $this->subCategories)) {
                $subCategory = $this->subCategories[$key];
            }
            else {

                $subCategory = new obu_course_hierarchy_info($category);
                $this->subCategories[$key] = $subCategory;
            }

            $subCategory->addCourse($course, $courseCategories);
        }
        else {
            $this->courses[] = $course;
        }
    }
}