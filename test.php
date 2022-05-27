<?php

use enrol_ethos\services\processing_service;

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

$trace = new html_progress_trace();
$processingService = new processing_service($trace);
$lastProcessedID = 0;
$maxProcessedID = 0;
$processingService->process_ethos_updates($lastProcessedID, $maxProcessedID);


