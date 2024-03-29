<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_course_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_course_provider extends ethos_provider
{
    const VERSION = 'v16';
    const PATH = 'courses';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION, 300);
    }

    private static ?ethos_course_provider $instance = null;
    public static function getInstance() : ethos_course_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_course_info {
        $item = $this->getFromEthosById($id);

        if(!$item || isset($item->errors)) {
            return null;
        }

        return $this->convert($item);
    }

    /**
     * @return ethos_course_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_course_info {
        return new ethos_course_info($item);
    }


}
