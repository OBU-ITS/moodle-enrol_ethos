<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_subject_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_subject_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('subjects', 'v6');
    }

    private static ?ethos_subject_provider $instance = null;
    public static function getInstance() : ethos_subject_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_subject_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_subject_info {
        return new ethos_subject_info($item);
    }

}
