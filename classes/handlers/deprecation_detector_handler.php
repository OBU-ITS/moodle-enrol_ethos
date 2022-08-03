<?php
namespace enrol_ethos\handlers;

use enrol_ethos\services\deprecation_detector_service;

class deprecation_detector_handler
{
    private deprecation_detector_service $deprecationdetectorService;
    private ethos_report_service $availableresourcesService;
    private processing_service $emailService;


    public function __construct()
    {
        $this->deprecationdetectorService = new deprecation_detector_service();
        $this->availableresourcesService = new available_resources_service();
        $this->emailService = new email_service();
    }
}