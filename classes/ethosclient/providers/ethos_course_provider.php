<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_course_provider extends ethos_provider
{
    private function __construct()
    {
        // TODO
    }

    private static ?ethos_course_provider $instance = null;
    public static function getInstance() : ethos_course_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
