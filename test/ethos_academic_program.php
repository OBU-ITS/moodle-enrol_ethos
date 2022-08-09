<?php

use enrol_ethos\services\sync\obu_sync_academic_program_service;

require_once('../../../config.php');
require_once($CFG->libdir . '/weblib.php');

$idQs = $_GET["id"] ?? '52c25d16-b9fe-416f-9d7a-bf4a07b766cf';

$start = microtime(true);
$trace = new html_progress_trace();;
$service = obu_sync_academic_program_service::getInstance();
$service->sync($trace, $idQs);

$end = microtime(true);

$totalTime = $end - $start;
$trace->output("----------------------");
$trace->output("Completed in $totalTime seconds");