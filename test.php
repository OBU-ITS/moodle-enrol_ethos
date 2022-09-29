<?php

use enrol_ethos\processors\base\obu_processor;
use enrol_ethos\services\deprecation_detector_service;

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

//$trace = new \html_progress_trace();
//$handler = new \enrol_ethos\handlers\deprecation_detector_handler($trace);
//
//$handler->handleDetectingDeprecations();

$service = deprecation_detector_service::getInstance();

$relevantResources = $service->getRelevantAvailableResources();
$deprecatedResources = $service->getDeprecatedResources($relevantResources);
foreach($deprecatedResources as $key=>$resource) {
    if($resource->deprecatedOn != ""){
        echo($key . " deprecated <br>");
    }
    if($resource->newVersionAvailable != ""){
        echo($key . " new version available: $resource->currentVersion > $resource->newVersionAvailable <br>");
    }
};