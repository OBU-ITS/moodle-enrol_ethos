<?php

namespace enrol_ethos\ethosclient\services;

use enrol_ethos\ethosclient\entities\ethos_section_title_info;

class ethos_section_title_service
{
    private function __construct()
    {
    }

    private static ?ethos_section_title_service $instance = null;
    public static function getInstance() : ethos_section_title_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($obj) : ethos_section_title_info {

        return new ethos_section_title_info($obj);
    }

}