<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_course;
use enrol_ethos\entities\obu_course_categories_info;
use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\ethosclient\entities\ethos_section_info;
use enrol_ethos\ethosclient\providers\ethos_section_provider;

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
     * @param string $id ethos guid
     * @return obu_course_hierarchy_info
     */
    public function get(string $id) : obu_course_hierarchy_info {
        $moduleRun = $this->sectionProvider->get($id);
        $moduleRuns = array($moduleRun);

        return $this->convertToMoodleCourseHierarchy($moduleRuns);
    }

    /**
     * Get all modules from Ethos
     *
     * @return obu_course_hierarchy_info
     */
    public function getAll() : obu_course_hierarchy_info {
        $moduleRuns = $this->sectionProvider->getAll();

        return $this->convertToMoodleCourseHierarchy($moduleRuns);
    }

    /**
     * @param ethos_section_info[] $moduleRuns
     * @return obu_course_hierarchy_info
     */
    private function convertToMoodleCourseHierarchy(array $moduleRuns) : obu_course_hierarchy_info {
        $hierarchy = obu_course_hierarchy_info::getTopCategory();

        foreach($moduleRuns as $moduleRun) {
            $this->addModuleRunToHierarchy($moduleRun, $hierarchy);
            $this->subjectGroupService->addSubjectGroupToHierarchy($moduleRun, $hierarchy);
        }

        return $hierarchy;
    }

    /**
     * @param ethos_section_info $moduleRun
     * @param obu_course_hierarchy_info $hierarchy
     */
    private function addModuleRunToHierarchy(ethos_section_info $moduleRun, obu_course_hierarchy_info $hierarchy) {
        if($moduleRun->number == "0") {
            return;
        }

        $course = $moduleRun->getCourse();
        $courseNumber = $course->number;

        $subject = $course->getSubject();
        $subjectCode = $subject->abbreviation;

        if($subjectCode == "FEE" || $subjectCode == "EXCH" || $subjectCode == "ACAD") {
            return;
        }

        $bannerSectionGuid = $moduleRun->id;
        $crn = $moduleRun->code;

        $subTerm = $moduleRun->getAcademicPeriod();
        $subTermCode = $subTerm->code;
        $subTermDescription = $subTerm->title;

        $term = $this->academicPeriodService->getTerm($subTerm);
        $termCode = $term->code;

        $year =  $this->academicPeriodService->getYear($term);
        $yearCode = $year->code;
        $yearDescription = $year->title;

        $site = $moduleRun->getSite();
        $campusCode = $site->code;

        $sectionNumber = $moduleRun->number;

        $longTitle = $this->titleService->getLongTitle($moduleRun->titles);

        $college = $this->collegeService->getCollege($moduleRun->owningInstitutionUnits);
        $department = $this->departmentService->getDepartment($moduleRun->owningInstitutionUnits);

        $idNumber = $this->getIdNumber($yearCode, $subjectCode, $courseNumber, $subTermCode, $sectionNumber);
        $shortName = $this->getShortName($subjectCode, $courseNumber, $termCode, $sectionNumber);
        $fullName = $this->getFullName($subjectCode, $courseNumber, $longTitle, $subTermDescription, $yearDescription, $sectionNumber, $campusCode);

        $course = new mdl_course($idNumber, $shortName, $fullName);
        $course->startdate = 0; // TODO
        $course->enddate = 0; // TODO

        $categories = new obu_course_categories_info($site, $college, $department, $subject);

        $hierarchy->addCourse($course, $categories);
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

        return $subjectCode . $courseNumber .": " . $longTitle . " (" . $subTermDescription . " " . $yearDescription . ":" . $sectionNumber . "[" . $campusCode . "]";
    }
}