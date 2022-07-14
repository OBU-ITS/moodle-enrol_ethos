<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_academic_credential_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('academic-credentials', 'v6');
    }

    private static ?ethos_academic_credential_provider $instance = null;
    public static function getInstance() : ethos_academic_credential_provider
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