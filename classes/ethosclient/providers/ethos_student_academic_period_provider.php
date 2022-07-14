<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_student_academic_period_provider extends ethos_provider
{
    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('student-academic-periods', 'v1');
    }

    private static ?ethos_student_academic_period_provider $instance = null;
    public static function getInstance() : ethos_student_academic_period_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getAcademicPeriodProfiles($personId, $academicPeriodId) : ?array {
        $url = $this->buildUrlWithCriteria("{\"person\":{\"id\":\"".$personId."\"}, \"academicPeriod\":{\"id\":\"".$academicPeriodId."\"}}");

        return $this->getFromEthos($url);
    }

    public function get($id) : ?object {
        return $this->getFromEthosById($id);
    }

    public function getAll() : ?array {
        return $this->getFromEthos();
    }
}