<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_course;
use enrol_ethos\entities\obu_course_categories_info;
use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\ethosclient\entities\ethos_academic_program_info;
use enrol_ethos\ethosclient\entities\ethos_section_info;
use enrol_ethos\ethosclient\providers\ethos_academic_program_provider;
use enrol_ethos\ethosclient\providers\ethos_section_provider;
use enrol_ethos\helpers\obu_datetime_helper;

class obu_program_service
{
    private const DEGREE_APPRENTICESHIP = "-DA";

    private ethos_academic_program_provider $academicProgramProvider;
    private obu_college_service $collegeService;

    private function __construct()
    {
        $this->academicProgramProvider = ethos_academic_program_provider::getInstance();
        $this->collegeService = obu_college_service::getInstance();
    }

    private static ?obu_program_service $instance = null;
    public static function getInstance(): obu_program_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param obu_course_hierarchy_info $hierarchy
     * @param string $id
     */
    public function get(obu_course_hierarchy_info $hierarchy, string $id) {
        $program = $this->academicProgramProvider->get($id);

        $this->addProgramToHierarchy($hierarchy, $program);
    }

    /**
     * @param obu_course_hierarchy_info $hierarchy
     * @param $limit
     * @param $offset
     * @return int
     */
    public function getBatch(obu_course_hierarchy_info $hierarchy, $limit, $offset) : int {
        $programs = $this->academicProgramProvider->getBatch($limit, $offset);

        array_map(function($program) use ($hierarchy) {
             $this->addProgramToHierarchy($hierarchy, $program);
        }, $programs);

        return count($programs);
    }

    /**
     * @param obu_course_hierarchy_info $hierarchy
     */
    public function getAll(obu_course_hierarchy_info $hierarchy) {
        $programs = $this->academicProgramProvider->getAll();

        array_map(function($program) use ($hierarchy) {
            $this->addProgramToHierarchy($hierarchy, $program);
        }, $programs);
    }

    /**
     * @param obu_course_hierarchy_info $hierarchy course hierarchy
     * @param ethos_academic_program_info $program
     */
    private function addProgramToHierarchy(obu_course_hierarchy_info $hierarchy, ethos_academic_program_info $program) {

        $sites = $program->getSites();
        $academicLevel = $program->getAcademicLevel();
        $academicCredential = $program->getAcademicCredentials()[0]; // TODO : Check with Jock
        $college = $this->collegeService->getCollege($program->getProgramOwners());
        $startDate = obu_datetime_helper::convertStringToTimeStamp('01-JAN-2019');

        foreach ($sites as $site){
            $idNumber = $this->getIdNumber($site->code, $academicLevel->code, $program->ump, $program->umpJoint, $program->code);
            $shortName = $this->getShortName($program->code, $site->code);
            if(substr_compare($program->code, self::DEGREE_APPRENTICESHIP, -3) === 0 ){
                $fullName = $this->getFullNameDA($academicCredential->title, $program->majorFullTitle, $site->title);
            } else{
                $fullName = $this->getFullName($academicCredential->title, $program->majorFullTitle, $site->title);
            }

            $course = new mdl_course($idNumber, $shortName, $fullName);
            $course->startdate = $startDate;
            $course->enddate = $startDate;
            $course->bannerId = $program->id;

            $categories = new obu_course_categories_info($site, $college, null, null);

            $hierarchy->addCourse($course, $categories->getCategories());
        }
    }

    /**
     * @param $siteCode
     * @param $academicLevelCode
     * @param $programUmp
     * @param $programUmpJoint
     * @param $academicProgramCode
     * @return string
     */
    private function getIdNumber($siteCode, $academicLevelCode, $programUmp, $programUmpJoint, $academicProgramCode) : string {

        return $siteCode . "~" . $academicLevelCode . "~" . $programUmp . "~" . $programUmpJoint . "#" . $academicProgramCode;
    }

    /**
     * @param $academicProgramCode
     * @param $siteCode
     * @return string
     */
    private function getShortName($academicProgramCode, $siteCode) : string {

        return $academicProgramCode . "[" . $siteCode . "]";
    }

    /**
     * @param $academicCredentialTitle
     * @param $majorFullTitle
     * @param $siteTitle
     * @return string
     */
    private function getFullNameDA($academicCredentialTitle, $majorFullTitle, $siteTitle) : string {

        return $academicCredentialTitle . ": " . $majorFullTitle . "[" . $siteTitle . "](" . "Degree Apprenticeship" . ")";
    }

    /**
     * @param $academicCredentialTitle
     * @param $majorFullTitle
     * @param $siteTitle
     * @return string
     */
    private function getFullName($academicCredentialTitle, $majorFullTitle, $siteTitle) : string {

        return $academicCredentialTitle . ": " . $majorFullTitle . "[" . $siteTitle . "]";
    }

}

