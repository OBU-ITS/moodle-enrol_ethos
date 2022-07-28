<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_student_type_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_student_type_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('student-types', 'v7');
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

        return $this->convert($item);
    }

    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map('convert', $items);
    }

    private function convert(object $item) : ?ethos_student_type_info {
        return new ethos_student_type_info($item);
    }
}