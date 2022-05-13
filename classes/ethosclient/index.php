<?php

require 'vendor/autoload.php';
require 'client/ethos_client.php';
require 'service/student_lookup_service.php';

$apiKey = '25d73674-4246-49f8-8aa9-52e5bc66446e';
$ethosClient = new EthosClient($apiKey);
$studentLookupService = new StudentLookupService($ethosClient);

//$students = $cw_api->getPersonByBannerId("DREWSID");
//$students = $cw_api->getStudents();

$time = microtime(true); // time in Microseconds
$student = $studentLookupService->lookupStudentFromPersonId("3fe5bbea-39e1-402e-9afe-ccb568d79529");
echo (microtime(true) - $time) . " elapsed\n";

//$time = microtime(true); // time in Microseconds
//$student = $studentLookupService->lookupStudentFromPersonId("7b808ba0-1272-43cb-83fc-c05cd3e697c6");
//echo (microtime(true) - $time) . " elapsed\n";
//
//$time = microtime(true); // time in Microseconds
//$student = $studentLookupService->lookupStudentFromPersonId("7b808ba0-1272-43cb-83fc-c05cd3e697c6");
//echo (microtime(true) - $time) . " elapsed\n";
//
//$time = microtime(true); // time in Microseconds
//$student = $studentLookupService->lookupStudentFromPersonId("7b808ba0-1272-43cb-83fc-c05cd3e697c6");
//echo (microtime(true) - $time) . " elapsed\n";

var_dump($student);