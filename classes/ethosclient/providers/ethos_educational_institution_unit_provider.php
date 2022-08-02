<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_educational_institution_unit_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_educational_institution_unit_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('educational-institution-units', 'v7');
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

    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_educational_institution_unit_info {
        return new ethos_educational_institution_unit_info($item);
    }
}