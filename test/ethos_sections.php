<?php

use enrol_ethos\handlers\sync_module_runs_handler;

require_once('../../../config.php');
require_once($CFG->libdir.'/weblib.php');

$maxStr = $_GET["max"] ?? '1';
$max = intval($maxStr);

$handler = new sync_module_runs_handler();
$handler->handleSyncAll($max);
