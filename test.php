<?php

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');


$service = \enrol_ethos\services\sync\obu_sync_person_service::getInstance();
$trace = new \html_progress_trace();
$service->sync($trace, 'f66812d3-f2de-4f10-a394-0f0ee3aeb61d');

echo  "Hello!";
echo "World.....";
echo "Goodbye";

