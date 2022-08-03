<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_student_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_student_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('students', 'v16');
    }

    private static ?ethos_student_provider $instance = null;
    public static function getInstance() : ethos_student_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ethos_student_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    /**
     * @param $personId
     * @return ethos_student_info[]
     */
    public function getStudentByPersonId($personId) : array {
        $url = $this->buildUrlWithCriteria('{"person": {"id": "'. $personId . '"}}');
        $items = $this->getFromEthos($url);
        return array_map(array($this, 'convert'), $items);
    }

    /**
     * @return ethos_student_info[]
     */
    public function getStudents() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    /**
     * @return ethos_student_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_student_info {
        return new ethos_student_info($item);
    }
}