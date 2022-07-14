<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_subject_provider extends ethos_provider
{
    private function __construct()
    {
        // TODO
    }

    private static ?ethos_subject_provider $instance = null;
    public static function getInstance() : ethos_subject_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // TODO : - functions
}
