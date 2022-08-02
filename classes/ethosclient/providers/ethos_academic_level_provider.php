<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_academic_level_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_academic_level_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('academic-levels', 'v6');
    }

    private static ?ethos_academic_level_provider $instance = null;
    public static function getInstance() : ethos_academic_level_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_academic_level_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_academic_level_info {
        return new ethos_academic_level_info($item);
    }
}