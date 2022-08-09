<?php

namespace enrol_ethos\ethosclient\services;

use enrol_ethos\ethosclient\entities\ethos_person_info_alternative_credential;

class ethos_person_alternative_credential_service
{
    private function __construct()
    {
    }

    private static ?ethos_person_alternative_credential_service $instance = null;
    public static function getInstance() : ethos_person_alternative_credential_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($obj) : ethos_person_info_alternative_credential {

        return new ethos_person_info_alternative_credential($obj);
    }

}