<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_student_academic_program_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_student_academic_program_provider extends ethos_provider
{
    const VERSION = 'v17.0.0';
    const PATH = 'student-academic-programs';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
    }

    private static ?ethos_student_academic_program_provider $instance = null;
    public static function getInstance() : ethos_student_academic_program_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_student_academic_program_info {
        $item = $this->getFromEthosById($id);

        if(!$item || isset($item->errors)) {
            return null;
        }

        return $this->convert($item);
    }

    /**
     * @param $personId
     * @return ethos_student_academic_program_info[]|null
     */
    public function getStudentAcademicProgramsByPersonId($personId) : ?array {
        $url = $this->buildUrlWithCriteria('{"student":{"id":"' . $personId . '"}}');
        $items = $this->getFromEthos($url);

        return array_map(array($this, 'convert'), $items);
    }

    /**
     * @return ethos_student_academic_program_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_student_academic_program_info {
        return new ethos_student_academic_program_info($item);
    }
}