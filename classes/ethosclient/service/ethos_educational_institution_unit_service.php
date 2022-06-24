<?php
namespace enrol_ethos\ethosclient\service;

class ethos_educational_institution_unit_service extends ethos_service
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareService('educational-institution-units', 'v7');
    }

    private static ?ethos_educational_institution_unit_service $instance = null;
    public static function getInstance() : ethos_educational_institution_unit_service
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