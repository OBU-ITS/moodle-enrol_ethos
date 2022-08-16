<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_person_hold_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_person_hold_provider extends ethos_provider
{
    const VERSION = 'v6';
    const PATH = 'person-holds';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
    }

    private static ?ethos_person_hold_provider $instance = null;
    public static function getInstance() : ethos_person_hold_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $id
     * @return ethos_person_hold_info|null
     */
    public function get($id) : ?ethos_person_hold_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    /**
     * @return ethos_person_hold_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    /**
     * @param $personId
     * @return ethos_person_hold_info[]
     */
    public function getByPersonGuid($personId) : array {
        $url = $this->buildUrlWithParameter('person', $personId);
        $items = $this->getFromEthos($url);
        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_person_hold_info {
        return new ethos_person_hold_info($item);
    }
}