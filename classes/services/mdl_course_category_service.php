<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\obu_course_category_info;

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

    public function ensureCourseCategory(obu_course_category_info $category, string $categoryId) {
        if($category->codeName == '0') {
            return;
        }

        echo "<br/>Upsert category <br/>";
        echo "Id Number : " . $categoryId . " <br/>";
        echo "Name : " . $category->name . " <br/>";
    }

    public function getCategoryId(string $keyPrefix, string $codeName) : string {
        if($codeName == '0') {
            return '';
        }

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