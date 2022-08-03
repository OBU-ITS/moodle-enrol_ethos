<?php
namespace enrol_ethos\ethosclient\services;

class deprecation_detector_service {

    private function __construct()
    {
    }

    private static ?deprecation_detector_service $instance = null;
    public static function getInstance() : deprecation_detector_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($obj) : deprecation_detector_service {

        return new deprecation_detector_service($obj);
    }
}