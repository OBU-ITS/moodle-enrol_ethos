<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_section_title_type_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_section_title_type_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('section-title-types', 'v1', 3600);
    }

    private static ?ethos_section_title_type_provider $instance = null;
    public static function getInstance() : ethos_section_title_type_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_section_title_type_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map('convert', $items);
    }

    private function convert(object $item) : ?ethos_section_title_type_info {
        return new ethos_section_title_type_info($item);
    }
}