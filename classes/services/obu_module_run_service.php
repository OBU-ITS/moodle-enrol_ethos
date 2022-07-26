<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_course;
use enrol_ethos\entities\obu_course_category_info;
use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\ethosclient\entities\ethos_section_info;
use enrol_ethos\ethosclient\providers\ethos_section_provider;

class obu_module_run_service
{
    private ethos_section_provider $sectionProvider;
    private obu_academic_period_service $academicPeriodService;
    private obu_module_run_title_service $titleService;

    public function __construct()
    {
        $this->sectionProvider = ethos_section_provider::getInstance();
        $this->academicPeriodService = obu_academic_period_service::getInstance();
        $this->titleService = obu_module_run_title_service::getInstance();
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

// TODO : implement skip functionality
//         if(true) {
//                sect.SSBSECT_SUBJ_CODE == 'FEE'
//                AND sect.SSBSECT_SUBJ_CODE == 'EXCH'
//                AND sect.SSBSECT_SEQ_NUMB == '0'
//             continue;
//      }

            $subTerm = $moduleRun->getAcademicPeriod();
            $subTermCode = $subTerm->code;
            $subTermDescription = $subTerm->title;
            $term = $this->academicPeriodService->getTerm($subTerm);
            $termCode = $term->code;
            $year =  $this->academicPeriodService->getYear($term);
            $yearCode = $year->code;
            $yearDescription = $year->title;

            $course = $moduleRun->getCourse();
            $courseNumber = $course->number;

            $subject = $course->getSubject();
            $subjectCode = $subject->abbreviation;

            $site = $moduleRun->getSite();
            $campusCode = $site->code;

            $sectionNumber = $moduleRun->number;
            $longTitle = $this->titleService->getLongTitle($moduleRun->titles);

            $idNumber = $this->getIdNumber($yearCode, $subjectCode, $courseNumber,$subTermCode, $sectionNumber);
            $shortName = $this->getShortName($subjectCode, $courseNumber, $termCode, $sectionNumber);
            $fullName = $this->getFullName($subjectCode, $courseNumber, $longTitle, $subTermDescription, $yearDescription, $sectionNumber, $campusCode);

            $course = new mdl_course($idNumber, $shortName, $fullName);
            $course->startdate = 0; // TODO
            $course->enddate = 0; // TODO


            $categories = $this->getCategories($moduleRun);

            $hierarchy->addCourse($course, $categories);
        }

        return $hierarchy;
    }

    /**
     * @param ethos_section_info $info
     * @return string
     */
    private function getIdNumber($yearCode, $subjectCode, $courseNumber,$subTermCode, $sectionNumber) : string {

        return $yearCode . "." . $subjectCode . $courseNumber . "_" . $subTermCode . "_" . $sectionNumber;
    }

    /**
     * @param ethos_section_info $info
     * @return string
     */
    private function getShortName($subjectCode, $courseNumber, $termCode, $sectionNumber) : string {

        return $subjectCode . $courseNumber . " (" . $termCode . ":" . $sectionNumber . ")";
    }

    /**
     * @param ethos_section_info $info
     * @return string
     */
    private function getFullName($subjectCode, $courseNumber, $longTitle, $subTermDescription, $yearDescription, $sectionNumber, $campusCode) : string {

        return $subjectCode . $courseNumber .": " . $longTitle . " (" . $subTermDescription . " " . $yearDescription . ":" . $sectionNumber . "[" . $campusCode . "]";
    }

    /**
     * @param ethos_section_info $info
     * @return obu_course_category_info[]
     */
    private function getCategories(ethos_section_info $info) : array {
        // TODO

        return array(
            new obu_course_category_info("SRS-Linked", "SRS", "SRS~~"),
            new obu_course_category_info("OBBS", "BU"),
            new obu_course_category_info("Business and Management", "BMGT")
        );
    }

}