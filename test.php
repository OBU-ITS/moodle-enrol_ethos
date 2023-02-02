<?php

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

use enrol_ethos\services\sync\obu_sync_person_service;

$service = obu_sync_person_service::getInstance();
$trace = new \html_progress_trace();

$service->sync($trace,"ea54fef8-893e-4751-bbda-557a46865088");