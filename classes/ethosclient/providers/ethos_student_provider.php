<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_student_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('students', 'v16');
    }

    private static ?ethos_student_provider $instance = null;
    public static function getInstance() : ethos_student_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getStudentById($id) : ethos_student_info {
        return $this->getFromEthosById($id);
    }

    /**
     * @param $personId
     * @return ethos_student_info[]
     */
    public function getStudentByPersonId($personId) : array {
        $url = $this->buildUrlWithCriteria('{"person": {"id": "'. $personId . '"}}');

        return $this->getFromEthos($url);
    }

    public function getStudents() : array {
        return $this->getFromEthos();
    }
}