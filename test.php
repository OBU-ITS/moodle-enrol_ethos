<?php

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

$trace = new html_progress_trace();
$handler = new \enrol_ethos\handlers\ethos_notifications_handler($trace);

$notification = new \enrol_ethos\ethosclient\entities\consume\ethos_notification();
$notification->resourceId = 'e6a06186-d1b1-4b08-8b01-3f29f8060f83';
$notification->operation="replaced";
$notification->resourceName="person-holds";
$notifications = new \enrol_ethos\ethosclient\entities\consume\ethos_notifications();
$notifications->addNotification($notification);

$handler->processNotificationGroup('person-holds', $notifications);


