<?php
namespace enrol_ethos\ethosclient\services;

use enrol_ethos\services\available_resources_service;
use enrol_ethos\services\email_service;

class deprecation_detector_service {

    private available_resources_service $availableresourcesService;
    private email_service $emailService;

    private function __construct()
    {
        $this->available_resources_service = new available_resources_service();
        $this->emailService = new email_service();
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
