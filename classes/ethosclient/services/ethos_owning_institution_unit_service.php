<?php
namespace enrol_ethos\ethosclient\services;

use enrol_ethos\ethosclient\entities\ethos_section_info_owning_institution_unit;

class ethos_owning_institution_unit_service {

    private function __construct()
    {
    }

    private static ?ethos_owning_institution_unit_service $instance = null;
    public static function getInstance() : ethos_owning_institution_unit_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($obj) : ethos_section_info_owning_institution_unit {

        return new ethos_section_info_owning_institution_unit($obj);
    }
}
