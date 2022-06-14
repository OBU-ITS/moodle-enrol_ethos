<?php

use enrol_ethos\services\ethos_report_service;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
$PAGE->set_url(new moodle_url('/enrol/ethos/reports/changes.php'));
$PAGE->set_pagelayout('report');

require_login();
$context = context_system::instance();
$PAGE->set_context($context);

require_capability('report/log:view', $context);

$PAGE->set_title('Ethos Notification report actions');

require_once($CFG->libdir.'/weblib.php');

if (!is_siteadmin())
{
    echo "Site Admin privilege required.";
    die();
}

$id = $_GET["id"] ?? 0;
if ($id == 0)
{
    echo "Invalid ID parse.";
    die();
}

admin_externalpage_setup('reportenrolethos', '', null, '', array('pagelayout' => 'report'));
$reportService = new ethos_report_service();

echo $OUTPUT->header();

$usersCreated = $reportService->getReportActions($id, "user", "create");
if(count($usersCreated) > 0) {
    echo "<h2 id='userscreated'>Users Created</h2>";
    echo "<ul>";
    foreach($usersCreated as $userCreated) {
        if(!isset($userCreated)) continue;
        echo "<li>{$userCreated->resource_description}</li>";
    }
    echo "</ul>";
}

$usersUpdated = $reportService->getReportActions($id, "user", "update");
if(count($usersUpdated) > 0) {
    echo "<h2 id='usersupdated'>Users Updated</h2>";
    echo "<ul>";
    foreach ($usersUpdated as $userUpdated) {
        if (!isset($userUpdated)) continue;
        echo "<li>{$userUpdated->resource_description}</li>";
    }
    echo "</ul>";
}

echo $OUTPUT->footer();