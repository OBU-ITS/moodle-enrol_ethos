<?php
namespace enrol_ethos\ethosclient\service;

class ethos_student_service extends ethos_service
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareService('students', 'v16');
    }

    private static ?ethos_student_service $instance = null;
    public static function getInstance() : ethos_student_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getStudentById($id) : object {
        return $this->getFromEthosById($id);
    }

    public function getStudentByPersonId($personId) : object {
        $url = $this->buildUrlWithCriteria('{"person": {"id": "'. $personId . '"}}');

        return current($this->getFromEthos($url));
    }

    public function getStudents() : array {
        return $this->getFromEthos();
    }
}