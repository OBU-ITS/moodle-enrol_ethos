<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_academic_discipline_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('academic-disciplines', 'v15');
    }

    private static ?ethos_academic_discipline_provider $instance = null;
    public static function getInstance() : ethos_academic_discipline_provider
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