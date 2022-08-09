<?php
namespace enrol_ethos\repositories;

use core_course_category;
use enrol_ethos\entities\obu_course_category_info;

class db_course_category_repository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get($idNumber) {
        if ($moodleCourseCategory = $this->db->get_record('course_categories', array('idnumber' => $idNumber))) {
            return core_course_category::get($moodleCourseCategory->id);
        } else {
            return false;
        }
    }

    public function getByNameAndParent($name, $parent) {
        if ($moodleCourseCategory = $this->db->get_record('course_categories', array('name' => $name, 'parent' => $parent))) {
            return $moodleCourseCategory;
        } else {
            return false;
        }
    }

    public function create(obu_course_category_info $category, string $categoryIdNumber, int $parentId) : core_course_category{
        $moodleCourseCategory = new \stdClass();
        $moodleCourseCategory->name = $category->name;
        $moodleCourseCategory->idnumber = $categoryIdNumber;
        $moodleCourseCategory->parent = $parentId;

        return core_course_category::create($moodleCourseCategory);
    }
}