<?php

require 'vendor/autoload.php';
require 'client/EthosClient.php';
require 'service/StudentLookupService.php';

$apiKey = '6046f9ea-e126-4cc7-b8a3-ad360afc5cb7';
$ethosClient = new EthosClient($apiKey);
$studentLookupService = new StudentLookupService($ethosClient);

//$students = $cw_api->getPersonByBannerId("DREWSID");
//$students = $cw_api->getStudents();

$time = microtime(true); // time in Microseconds
$student = $studentLookupService->lookupStudentFromPersonId("7b808ba0-1272-43cb-83fc-c05cd3e697c6");
echo (microtime(true) - $time) . " elapsed\n";

$time = microtime(true); // time in Microseconds
$student = $studentLookupService->lookupStudentFromPersonId("7b808ba0-1272-43cb-83fc-c05cd3e697c6");
echo (microtime(true) - $time) . " elapsed\n";

$time = microtime(true); // time in Microseconds
$student = $studentLookupService->lookupStudentFromPersonId("7b808ba0-1272-43cb-83fc-c05cd3e697c6");
echo (microtime(true) - $time) . " elapsed\n";

$time = microtime(true); // time in Microseconds
$student = $studentLookupService->lookupStudentFromPersonId("7b808ba0-1272-43cb-83fc-c05cd3e697c6");
echo (microtime(true) - $time) . " elapsed\n";

var_dump($student);