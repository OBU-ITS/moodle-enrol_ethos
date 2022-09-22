<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\entities\ethos_section_instructors_info;
use enrol_ethos\helpers\obu_datetime_helper;

class obu_section_instructor_service
{
    private function __construct()
    {
    }

    private static ?obu_section_instructor_service $instance = null;

    public static function getInstance(): obu_section_instructor_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param ethos_section_instructors_info[] $sectionInstructors
     * @return ethos_section_instructors_info[]
     */
    public function getCurrentOrFutureInstructors(array $sectionInstructors) : array {
        return array_filter($sectionInstructors, function($sectionInstructor) {
            return obu_datetime_helper::convertStringToTimeStamp($sectionInstructor->workEndOn) >= time();
        });
    }
}