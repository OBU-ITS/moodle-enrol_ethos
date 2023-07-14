<?php

use enrol_ethos\ethosclient\services\ethos_notification_service;

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

//use enrol_ethos\handlers\ethos_notifications_handler;
//
//$handler = new ethos_notifications_handler($trace);
//
//$handler->handleNotifications(10);


//$trace = new \html_progress_trace();
//$consumeService = ethos_notification_service::getInstance();
//$lastProcessId = 0;
//$processedCount = 0;
//
//$limit =  ethos_notification_service::CONSUME_LIMIT;
//
//$notifications = $consumeService->consumeMessages($trace, $lastProcessId, $limit);