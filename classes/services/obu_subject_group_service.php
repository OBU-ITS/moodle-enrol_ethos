<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_course;
use enrol_ethos\entities\obu_course_categories_info;
use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\ethosclient\entities\ethos_section_info;

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

    public function addSubjectGroupToHierarchy(ethos_section_info $moduleRun, obu_course_hierarchy_info $hierarchy) {
        if($moduleRun->number == "0") {
            return;
        }

        $course = $moduleRun->getCourse();
        $subject = $course->getSubject();
        $subjectCode = $subject->abbreviation;
        $subjectDescription = $subject->title;

        if($subjectCode == "FEE" || $subjectCode == "EXCH" || $subjectCode == "ACAD") {
            return;
        }

        $site = $moduleRun->getSite();
        $campusCode = $site->code;
        $campusDescription = $site->title;

        $college = $this->collegeService->getCollege($moduleRun->owningInstitutionUnits);
        $department = $this->departmentService->getDepartment($moduleRun->owningInstitutionUnits);

        $levels = $moduleRun->getAcademicLevels();
        foreach ($levels as $level) {
            $levelCode = $level->code;
            $levelDescription = $level->title;

            $idNumber = $this->getIdNumber($campusCode, $levelCode, $subjectCode);
            $shortName = $this->getShortName($subjectCode, $campusCode, $levelCode);
            $fullName = $this->getFullName($subjectCode, $levelDescription, $subjectDescription, $campusDescription);

            $course = new mdl_course($idNumber, $shortName, $fullName);
            $course->startdate = 0; // TODO
            $course->enddate = 0; // TODO

            $categories = new obu_course_categories_info($site, $college, $department, $subject);

            $hierarchy->addCourse($course, $categories);
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