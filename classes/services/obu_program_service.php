<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\ethosclient\entities\ethos_academic_program_info;
use enrol_ethos\ethosclient\entities\ethos_section_info;
use enrol_ethos\ethosclient\providers\ethos_academic_program_provider;
use enrol_ethos\ethosclient\providers\ethos_section_provider;

class obu_program_service
{
    private ethos_academic_program_provider $academicProgramProvider;

    private function __construct()
    {
        $this->academicProgramProvider = ethos_academic_program_provider::getInstance();
    }

    private static ?obu_program_service $instance = null;
    public static function getInstance(): obu_program_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(obu_course_hierarchy_info $hierarchy, string $id) {
        $program = $this->academicProgramProvider->get($id);

        $this->addProgramToHierarchy($hierarchy, $program);
    }

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
        $academicprogramcode = $program->code;
        $ump = $program->ump;
        $umpjoint = $program->umpJoint;
        $academiclevel = $program->getAcademicLevel();
        $MajorFullTitle = $program->majorFullTitle;
        $AcademicCredentials = $program->getAcademicCredentials();

        foreach ($sites as $site){
            $idNumber = $this->getIdNumber($site->code, $academiclevel->code, $ump, $umpjoint, $academicprogramcode);
            $shortName = $this->getShortName($academicprogramcode, $site->code);
            if(substr_compare($academicprogramcode, "-DA", -3) === 0 ){
                $fullName = $this->getFullNameDA($AcademicCredentials->title, $MajorFullTitle, $site->title);
            } else{
                $fullName = $this->getFullName($AcademicCredentials->title, $MajorFullTitle, $site->title);
            }

        }
    }

    /**
     * @param $siteCode
     * @param $academiclevelCode
     * @param $programUmp
     * @param $programUmpJoint
     * @param $academicprogramCode
     * @return string
     */
    private function getIdNumber($siteCode, $academiclevelCode, $programUmp, $programUmpJoint, $academicprogramCode) : string {

        return $siteCode . "~" . $academiclevelCode . "~" . $programUmp . "~" . $programUmpJoint . "#" . $academicprogramCode;
    }

    /**
     * @param $academicprogramcode
     * @param $siteCode
     * @return string
     */
    private function getShortName($academicprogramcode, $siteCode) : string {

        return $academicprogramcode . "[" . $siteCode . "]";
    }

    /**
     * @param $academiccredentialtitle
     * @param $majorfulltitle
     * @param $sitetitle
     * @return string
     */
    private function getFullNameDA($academiccredentialtitle, $majorfulltitle, $sitetitle) : string {

        return $academiccredentialtitle . ": " . $majorfulltitle . "[" . $sitetitle . "](" . "Degree Apprenticeship" . ")";
    }

    /**
     * @param $academiccredentialtitle
     * @param $majorfulltitle
     * @param $sitetitle
     * @return string
     */
    private function getFullName($academiccredentialtitle, $majorfulltitle, $sitetitle) : string {

        return $academiccredentialtitle . ": " . $majorfulltitle . "[" . $sitetitle . "]";
    }

}

