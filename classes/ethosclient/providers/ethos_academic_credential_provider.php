<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_academic_credential_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_academic_credential_provider extends ethos_provider
{
    const VERSION = 'v6';
    const PATH = 'academic-credentials';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
    }

    private static ?ethos_academic_credential_provider $instance = null;
    public static function getInstance() : ethos_academic_credential_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_academic_credential_info {
        $item = $this->getFromEthosById($id);

        if(!$item || isset($item->errors)) {
            return null;
        }

        return $this->convert($item);
    }

    /**
     * @return ethos_academic_credential_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_academic_credential_info {
        return new ethos_academic_credential_info($item);
    }
}