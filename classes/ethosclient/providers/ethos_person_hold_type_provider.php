<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_person_hold_type_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_person_hold_type_provider extends ethos_provider
{
    const VERSION = 'v6';
    const PATH = 'person-hold-types';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
    }

    private static ?ethos_person_hold_type_provider $instance = null;
    public static function getInstance() : ethos_person_hold_type_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_person_hold_type_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    /**
     * @return ethos_person_hold_type_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_person_hold_type_info {
        return new ethos_person_hold_type_info($item);
    }

}