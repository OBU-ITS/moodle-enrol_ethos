<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_course_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_course_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('courses', 'v16');
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

        return $this->convert($item);
    }

    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_course_info {
        return new ethos_course_info($item);
    }


}
