<?php

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

$trace = new html_progress_trace();
$handler = new \enrol_ethos\handlers\ethos_notifications_handler($trace);

$notification = new \enrol_ethos\ethosclient\entities\consume\ethos_notification();
$notification->resourceId = '820464a9-e506-44c3-87eb-535ca85a82bc';
$notification->operation="replaced";
$notification->resourceName="person-holds";
$notifications = new \enrol_ethos\ethosclient\entities\consume\ethos_notifications();
$notifications->addNotification($notification);

$handler->processNotificationGroup('person-holds', $notifications);


