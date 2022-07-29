<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_course;
use enrol_ethos\entities\obu_course_categories_info;
use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\ethosclient\entities\ethos_section_info;
use enrol_ethos\helpers\obu_datetime_helper;

class obu_subject_group_service
{
    private obu_college_service $collegeService;
    private obu_department_service $departmentService;

    private function __construct()
    {
        $this->collegeService = obu_college_service::getInstance();
        $this->departmentService = obu_department_service::getInstance();
    }

    private static ?obu_subject_group_service $instance = null;
    public static function getInstance(): obu_subject_group_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addSubjectGroupToHierarchy(obu_course_hierarchy_info $hierarchy, ethos_section_info $moduleRun) {
        if($moduleRun->number == "0") {
            return;
        }

        $course = $moduleRun->getCourse();
        $subject = $course->getSubject();

        if($subject->abbreviation == "FEE" || $subject->abbreviation == "EXCH" || $subject->abbreviation == "ACAD") {
            return;
        }

        $site = $moduleRun->getSite();
        $college = $this->collegeService->getCollege($moduleRun->owningInstitutionUnits);
        $department = $this->departmentService->getDepartment($moduleRun->owningInstitutionUnits);

        $levels = $moduleRun->getAcademicLevels();
        foreach ($levels as $level) {
            $idNumber = $this->getIdNumber($site->code, $level->code, $subject->abbreviation);
            $shortName = $this->getShortName($subject->abbreviation, $site->code, $level->code);
            $fullName = $this->getFullName($subject->abbreviation, $level->title, $subject->title, $site->title);

            $course = new mdl_course($idNumber, $shortName, $fullName);
            $course->startdate = obu_datetime_helper::convertStringToTimeStamp('01-JAN-2019');
            $course->enddate = 0;

            $categories = new obu_course_categories_info($site, $college, $department, $subject);

            $hierarchy->addCourse($course, $categories->getCategories());
        }
    }


    private function getIdNumber($campusCode, $levelCode, $subjectCode) : string {
        return '$' . $campusCode . '~' . $levelCode . '~' . $subjectCode;
    }

    private function getShortName($subjectCode, $campusCode, $levelCode) : string {
        return $subjectCode . '[' . $campusCode . '-' . $levelCode . ']';
    }

    private function getFullName($subjectCode, $levelDescription, $subjectDescription, $campusDescription) : string {
        return $subjectCode . ': ' . $levelDescription . ' ' . $subjectDescription . ' Subject Group Space [' . $campusDescription . ']';
    }
}