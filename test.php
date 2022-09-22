<?php

use enrol_ethos\services\obu_person_hold_service;


require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

$trace = new html_progress_trace();
$handler = new \enrol_ethos\handlers\ethos_notifications_handler($trace);

$notification = new \enrol_ethos\ethosclient\entities\consume\ethos_notification();
$notification->resourceId = 'c1fa8640-079f-49eb-883e-f74f0fbanana';
$notification->operation="replaced";
$notification->resourceName="person-holds";
$notifications = new \enrol_ethos\ethosclient\entities\consume\ethos_notifications();
$notifications->addNotification($notification);

$handler->processNotificationGroup('person-holds', $notifications);

$service = obu_person_hold_service::getInstance();
$temp = $service->cleanHoldsProfileField("[]");
if ($temp === ""){
    echo("no data found");
}
