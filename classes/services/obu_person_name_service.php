<?php
namespace enrol_ethos\services;


use enrol_ethos\ethosclient\entities\ethos_person_info_credential;
use enrol_ethos\ethosclient\entities\ethos_person_info_name;

class obu_person_name_service
{

    private function __construct()
    {
    }

    private static ?obu_person_name_service $instance = null;
    public static function getInstance(): obu_person_name_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param ethos_person_info_name[] $names
     * @return ethos_person_info_name
     */
    public function getOfficialName(array $names) : ethos_person_info_name {
        return array_filter($names, function ($name) {
            return ($name->category == '');
        })[0];
    }

    /**
     * @param ethos_person_info_name[] $names
     * @return ethos_person_info_name
     */
    public function getPreferredName(array $names) : ethos_person_info_name {
        $preferredNameValue = array_values(array_filter($names, function ($name) {
            return ($name->category == "favored");
        }))[0];

        if (!$preferredNameValue){
            $preferredNameValue = $this->getOfficialName($names);
        }

        return $preferredNameValue;
    }
}
