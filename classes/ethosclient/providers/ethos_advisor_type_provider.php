<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_advisor_type_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_advisor_type_provider extends ethos_provider
{
    const VERSION = 'v8';
    const PATH = 'advisor-types';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
    }

    private static ?ethos_advisor_type_provider $instance = null;
    public static function getInstance() : ethos_advisor_type_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_advisor_type_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    /**
     * @return ethos_advisor_type_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_advisor_type_info {
        return new ethos_advisor_type_info($item);
    }
}