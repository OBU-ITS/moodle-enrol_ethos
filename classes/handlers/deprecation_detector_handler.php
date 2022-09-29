<?php
namespace enrol_ethos\handlers;

use enrol_ethos\services\deprecation_detector_service;
use enrol_ethos\services\obu_email_service;
use progress_trace;

class deprecation_detector_handler
{
    private deprecation_detector_service $deprecationDetectorService;
    private obu_email_service $emailService;
    private progress_trace $trace;

    public function __construct($trace)
    {
        $this->deprecationDetectorService = deprecation_detector_service::getInstance();
        $this->emailService = obu_email_service::getInstance();

        $this->trace = $trace;
    }

    public function handleDetectingDeprecations() {
        $relevantResources = $this->deprecationDetectorService->getRelevantAvailableResources();
        $deprecatedResources = $this->deprecationDetectorService->getDeprecatedResources($relevantResources);

        if (count($deprecatedResources) == 0) {
            $this->trace->output("No deprecated resources detected");
            return;
        }

        else{
            $this->emailService->createEmailMessage($deprecatedResources);
            $this->emailService->sendEmail();
        }
    }
}