<?php

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

use enrol_ethos\services\sync\obu_sync_person_service;

$service = obu_sync_person_service::getInstance();
$trace = new \html_progress_trace();

$service->sync($trace,"5cf61482-117c-43f7-98a6-09dd9acc4c2a");