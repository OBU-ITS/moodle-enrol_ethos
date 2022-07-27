<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\obu_course_category_info;
use enrol_ethos\entities\obu_course_hierarchy_info;

class mdl_course_category_service
{
    private function __construct()
    {
    }

    private static ?mdl_course_category_service $instance = null;
    public static function getInstance(): mdl_course_category_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function ensureCourseCategory(obu_course_category_info $category, string $keyPrefix) {

    }
}