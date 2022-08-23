<?php
namespace enrol_ethos\services;


use enrol_ethos\ethosclient\services\ethos_available_resources_service;


class deprecation_detector_service {

    private ethos_available_resources_service $availableResourcesService;
    private email_service $emailService;

    private function __construct()
    {
        $this->availableResourcesService = ethos_available_resources_service::getInstance();
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

    public function get($obj) {

        return 0;
    }
}
