<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_section_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_section_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('sections', 'v16');
    }

    private static ?ethos_section_provider $instance = null;
    public static function getInstance() : ethos_section_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_section_info {
        $item = $this->getFromEthosById($id);

        return $this->convertToSection($item);
    }

    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map('convertToSection', $items);
    }

    private function convertToSection(object $item) : ?ethos_section_info {
        return new ethos_section_info();
    }
}