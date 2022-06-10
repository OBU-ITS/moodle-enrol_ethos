<?php

use enrol_ethos\handlers\ethos_notifications_handler;

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

$trace = new html_progress_trace();

$handler = new ethos_notifications_handler($trace);
$handler->handleNotifications();
