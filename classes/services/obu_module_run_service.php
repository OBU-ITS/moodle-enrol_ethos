<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_course;
use enrol_ethos\entities\obu_course_categories_info;
use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\ethosclient\entities\ethos_section_info;
use enrol_ethos\ethosclient\providers\ethos_section_provider;
use enrol_ethos\helpers\obu_datetime_helper;

class obu_module_run_service
{
    private ethos_section_provider $sectionProvider;
    private obu_academic_period_service $academicPeriodService;
    private obu_module_run_title_service $titleService;
    private obu_college_service $collegeService;
    private obu_department_service $departmentService;
    private obu_subject_group_service $subjectGroupService;

    private function __construct()
    {
        $this->sectionProvider = ethos_section_provider::getInstance();
        $this->academicPeriodService = obu_academic_period_service::getInstance();
        $this->titleService = obu_module_run_title_service::getInstance();
        $this->collegeService = obu_college_service::getInstance();
        $this->departmentService = obu_department_service::getInstance();
        $this->subjectGroupService = obu_subject_group_service::getInstance();
    }

    private static ?obu_module_run_service $instance = null;
    public static function getInstance(): obu_module_run_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get module from Ethos by the Ethos section guid
     *
     * @param obu_course_hierarchy_info $hierarchy course hierarchy
     * @param string $id ethos guid
     */
    public function get(obu_course_hierarchy_info $hierarchy, string $id) {
        $moduleRun = $this->sectionProvider->get($id);
        $moduleRuns = array($moduleRun);

        $this->addToCourseHierarchy($hierarchy, $moduleRuns);
    }

    /**
     * Get all modules from Ethos
     *
     * @param obu_course_hierarchy_info $hierarchy course hierarchy
     */
    public function getAll(obu_course_hierarchy_info $hierarchy) {
        $moduleRuns = $this->sectionProvider->getAll();

        $this->addToCourseHierarchy($hierarchy, $moduleRuns);
    }

    /**
     * @param obu_course_hierarchy_info $hierarchy course hierarchy
     * @param ethos_section_info[] $moduleRuns
     */
    private function addToCourseHierarchy(obu_course_hierarchy_info $hierarchy, array $moduleRuns) {
        foreach($moduleRuns as $moduleRun) {
            $this->addModuleRunToHierarchy($hierarchy, $moduleRun);
            $this->subjectGroupService->addSubjectGroupToHierarchy($hierarchy, $moduleRun);
        }
    }

    /**
     * @param obu_course_hierarchy_info $hierarchy
     * @param ethos_section_info $moduleRun
     */
    private function addModuleRunToHierarchy(obu_course_hierarchy_info $hierarchy, ethos_section_info $moduleRun) {
        if($moduleRun->number == "0") {
            return;
        }

        $course = $moduleRun->getCourse();
        $subject = $course->getSubject();

        if($subject->abbreviation == "FEE" || $subject->abbreviation == "EXCH" || $subject->abbreviation == "ACAD") {
            return;
        }

        $bannerSectionGuid = $moduleRun->id;
        $crn = $moduleRun->code;

        $subTerm = $moduleRun->getAcademicPeriod();
        $term = $this->academicPeriodService->getTerm($subTerm);
        $year =  $this->academicPeriodService->getYear($term);
        $site = $moduleRun->getSite();
        $longTitle = $this->titleService->getLongTitle($moduleRun->titles);
        $college = $this->collegeService->getCollege($moduleRun->owningInstitutionUnits);
        $department = $this->departmentService->getDepartment($moduleRun->owningInstitutionUnits);

        $idNumber = $this->getIdNumber($year->code, $subject->abbreviation, $course->number, $subTerm->code, $moduleRun->number);
        $shortName = $this->getShortName($subject->abbreviation, $course->number, $term->code, $moduleRun->number);
        $fullName = $this->getFullName($subject->abbreviation, $course->number, $longTitle, $subTerm->title, $year->title, $moduleRun->number, $site->code);

        $course = new mdl_course($idNumber, $shortName, $fullName);
        $course->startdate = obu_datetime_helper::convertStringToTimeStamp($subTerm->startOn);
        $course->enddate = obu_datetime_helper::convertStringToTimeStamp($subTerm->endOn);

        $categories = new obu_course_categories_info($site, $college, $department, $subject);

        $hierarchy->addCourse($course, $categories->getCategories());
    }

    /**
     * @param $yearCode
     * @param $subjectCode
     * @param $courseNumber
     * @param $subTermCode
     * @param $sectionNumber
     * @return string
     */
    private function getIdNumber($yearCode, $subjectCode, $courseNumber, $subTermCode, $sectionNumber) : string {

        return $yearCode . "." . $subjectCode . $courseNumber . "_" . $subTermCode . "_" . $sectionNumber;
    }

    /**
     * @param $subjectCode
     * @param $courseNumber
     * @param $termCode
     * @param $sectionNumber
     * @return string
     */
    private function getShortName($subjectCode, $courseNumber, $termCode, $sectionNumber) : string {

        return $subjectCode . $courseNumber . " (" . $termCode . ":" . $sectionNumber . ")";
    }

    /**
     * @param $subjectCode
     * @param $courseNumber
     * @param $longTitle
     * @param $subTermDescription
     * @param $yearDescription
     * @param $sectionNumber
     * @param $campusCode
     * @return string
     */
    private function getFullName($subjectCode, $courseNumber, $longTitle, $subTermDescription, $yearDescription, $sectionNumber, $campusCode) : string {

        return $subjectCode . $courseNumber .": " . $longTitle . " (" . $subTermDescription . " " . $yearDescription . ":" . $sectionNumber . "[" . $campusCode . "])";
    }
}