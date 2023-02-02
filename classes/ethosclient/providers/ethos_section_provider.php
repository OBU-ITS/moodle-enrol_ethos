<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_section_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_section_provider extends ethos_provider
{
    const VERSION = 'v16.0.0';
    const PATH = 'sections';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
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

        if(!$item || isset($item->errors)) {
            return null;
        }

        return $this->convert($item);
    }

    /**
     * @param $limit
     * @param $offset
     * @return ethos_section_info[]
     */
    public function getBatch($limit, $offset) : array {
        $items = $this->getFromEthos(null, true, $limit, $offset);

        return array_map(array($this, 'convert'), $items);
    }

    /**
     * @return ethos_section_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_section_info {
        return new ethos_section_info($item);
    }
}
