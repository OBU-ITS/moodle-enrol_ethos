<?php


use enrol_ethos\ethosclient\services\ethos_notification_service;

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

$service = ethos_notification_service::getInstance();
$notifications = $service->consumeMessages("0", 1);
