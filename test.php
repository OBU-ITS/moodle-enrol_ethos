<?php

use enrol_ethos\handlers\sync_module_runs_handler;

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

$handler = new sync_module_runs_handler();
$handler->handleSync('52c25d16-b9fe-416f-9d7a-bf4a07b766cf');
