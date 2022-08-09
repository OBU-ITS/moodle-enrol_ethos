<?php

use enrol_ethos\handlers\sync_module_runs_handler;

require_once('../../../config.php');
require_once($CFG->libdir.'/weblib.php');

$maxStr = $_GET["max"] ?? '1';
$max = intval($maxStr);

$start = microtime(true);
$trace = new html_progress_trace();
$handler = new sync_module_runs_handler($trace);
$handler->handleSyncAll($max);

$end= microtime(true);

$totalTime = $end - $start;
$trace->output("----------------------");
$trace->output("Completed in $totalTime seconds");
