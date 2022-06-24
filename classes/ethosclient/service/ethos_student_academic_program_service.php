<?php
namespace enrol_ethos\ethosclient\service;

class ethos_student_academic_program_service extends ethos_service
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareService('student-academic-programs', 'v17');
    }

    private static ?ethos_student_academic_program_service $instance = null;
    public static function getInstance() : ethos_student_academic_program_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getStudentAcademicProgram($id) : ?object {
        return $this->getFromEthosById($id);
    }

    public function getStudentAcademicProgramsByPersonId($personId) : ?array {
        $url = $this->buildUrlWithCriteria('{"student":{"id":"' . $personId . '"}}');

        return $this->getFromEthos($url);
    }
}