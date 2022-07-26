<?php
namespace enrol_ethos\handlers;


use enrol_ethos\services\deprecation_detector_service;

class deprecation_detector_handler
{
    private deprecation_detector_service $deprecationdetectorService;

    public function __construct($trace)
    {
        $this->deprecationdetectorService = new deprecation_detector_service($trace);
    }

    public function handleBackFill() {
        $this->deprecationdetectorService->backfillUserBannerGuids();
    }
}