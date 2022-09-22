<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_site_info;
use enrol_ethos\ethosclient\entities\ethos_student_academic_period_status_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_student_academic_period_status_provider extends ethos_provider
{
    const VERSION = 'v1';
    const PATH = 'student-academic-period-statuses';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
    }

    private static ?ethos_student_academic_period_status_provider $instance = null;
    public static function getInstance() : ethos_student_academic_period_status_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_student_academic_period_status_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    /**
     * @return ethos_student_academic_period_status_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_student_academic_period_status_info {
        return new ethos_student_academic_period_status_info($item);
    }
}