<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_student_academic_program_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('student-academic-programs', 'v17');
    }

    private static ?ethos_student_academic_program_provider $instance = null;
    public static function getInstance() : ethos_student_academic_program_provider
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