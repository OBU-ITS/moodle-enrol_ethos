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

    public function __construct()
    {
        $this->sectionProvider = ethos_section_provider::getInstance();
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
            $idNumber = $this->getIdNumber($moduleRun);
            $shortName = $this->getShortName($moduleRun);
            $fullName = $this->getFullName($moduleRun);

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
    private function getIdNumber(ethos_section_info $info) : string {
        // TODO

        return '';
    }

    /**
     * @param ethos_section_info $info
     * @return string
     */
    private function getShortName(ethos_section_info $info) : string {
        // TODO

        return '';
    }

    /**
     * @param ethos_section_info $info
     * @return string
     */
    private function getFullName(ethos_section_info $info) : string {
        // TODO

        return '';
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