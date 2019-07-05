<?php
namespace enrol_ethos\repositories;
use enrol_ethos\interfaces\course_category_repository_interface;

class db_course_category_repository implements course_category_repository_interface
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findOne($idNumber) {
        if ($moodleCourseCategory = $this->db->get_record('course_categories', array('idnumber' => $idNumber))) {
            return $moodleCourseCategory;
        } else {
            return false;
        }
    }

    public function findOneByNameAndParent($name, $parent) {
        if ($moodleCourseCategory = $this->db->get_record('course_categories', array('name' => $name, 'parent' => $parent))) {
            return $moodleCourseCategory;
        } else {
            return false;
        }
    }
}