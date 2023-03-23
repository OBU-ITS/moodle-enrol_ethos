<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_student_type_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_student_type_provider extends ethos_provider
{
    const VERSION = 'v7';
    const PATH = 'student-types';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
    }

    private static ?ethos_student_type_provider $instance = null;
    public static function getInstance() : ethos_student_type_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_student_type_info {
        $item = $this->getFromEthosById($id);

        if(!$item || isset($item->errors)) {
            return null;
        }

        return $this->convert($item);
    }

    /**
     * @return ethos_student_type_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_student_type_info {
        return new ethos_student_type_info($item);
    }
}