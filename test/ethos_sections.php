<?php

use enrol_ethos\services\sync\obu_sync_section_service;

require_once('../../../config.php');
require_once($CFG->libdir.'/weblib.php');

$maxStr = $_GET["max"] ?? '1';
$max = intval($maxStr);

$start = microtime(true);
$trace = new html_progress_trace();
$service = obu_sync_section_service::getInstance();
$service->syncAll($trace, $max);

$end= microtime(true);

$totalTime = $end - $start;
$trace->output("----------------------");
$trace->output("Completed in $totalTime seconds");
