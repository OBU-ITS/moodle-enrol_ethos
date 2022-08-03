<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_site_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_site_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('sites', 'v6');
    }

    private static ?ethos_site_provider $instance = null;
    public static function getInstance() : ethos_site_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_site_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    /**
     * @return ethos_site_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_site_info {
        return new ethos_site_info($item);
    }
}