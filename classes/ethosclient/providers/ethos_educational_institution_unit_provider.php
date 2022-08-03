<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_educational_institution_unit_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_educational_institution_unit_provider extends ethos_provider
{
    const VERSION = 'v7';
    const PATH = 'educational-institution-units';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION, 3600);
    }

    private static ?ethos_educational_institution_unit_provider $instance = null;
    public static function getInstance() : ethos_educational_institution_unit_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_educational_institution_unit_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    /**
     * @return ethos_educational_institution_unit_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_educational_institution_unit_info {
        return new ethos_educational_institution_unit_info($item);
    }
}