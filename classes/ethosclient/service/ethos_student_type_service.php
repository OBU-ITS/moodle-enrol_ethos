<?php
namespace enrol_ethos\ethosclient\service;

class ethos_student_type_service extends ethos_service
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareService('student-types', 'v7');
    }

    private static ?ethos_student_type_service $instance = null;
    public static function getInstance() : ethos_student_type_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?object {
        return $this->getFromEthosById($id);
    }

    public function getAll() : ?array {
        return $this->getFromEthos();
    }
}