<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_academic_discipline_info;
use enrol_ethos\ethosclient\entities\ethos_site_info;
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

    public function get($id) : ?ethos_academic_discipline_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    /**
     * @return ethos_academic_discipline_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_academic_discipline_info {
        return new ethos_academic_discipline_info($item);
    }
}