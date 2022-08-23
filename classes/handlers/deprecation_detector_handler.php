<?php
namespace enrol_ethos\handlers;

use enrol_ethos\services\deprecation_detector_service;

class deprecation_detector_handler
{
    private deprecation_detector_service $deprecationDetectorService;
    private available_resources_service $availableResourcesService;
    private email_service $emailService;


    public function __construct()
    {
        $this->deprecationDetectorService = new deprecation_detector_service();
        $this->availableResourcesService = new available_resources_service();
        $this->emailService = new email_service();
    }

    private function getAvailableResources(){
        $this->availableResourcesService->getAvailableResourcesService();
    }
}