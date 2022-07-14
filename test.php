<?php

use enrol_ethos\services\academic_program_service;

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

$service = new academic_program_service();
$programs = $service->getAllAcademicPrograms();

echo(count($programs));