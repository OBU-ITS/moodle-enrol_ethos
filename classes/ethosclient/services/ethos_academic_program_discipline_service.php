<?php

namespace enrol_ethos\ethosclient\services;

use enrol_ethos\ethosclient\entities\ethos_academic_program_info_discipline;

class ethos_academic_program_discipline_service
{
    private function __construct()
    {
    }

    private static ?ethos_academic_program_discipline_service $instance = null;
    public static function getInstance() : ethos_academic_program_discipline_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($obj) : ethos_academic_program_info_discipline {

        return new ethos_academic_program_info_discipline($obj);
    }

}