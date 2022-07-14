<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_academic_program_provider extends ethos_provider
{
    const VERSION = 'v15';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('academic-programs', self::VERSION);
    }

    private static ?ethos_academic_program_provider $instance = null;
    public static function getInstance() : ethos_academic_program_provider
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