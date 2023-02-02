<?php

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

use enrol_ethos\services\sync\obu_sync_person_service;

$service = obu_sync_person_service::getInstance();
$trace = new \html_progress_trace();

$service->sync($trace,"4408b376-4d47-42f8-929d-5ed51e14bed1");