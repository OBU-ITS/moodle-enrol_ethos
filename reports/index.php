<?php

use enrol_ethos\services\ethos_report_service;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
$PAGE->set_url(new moodle_url('/enrol/ethos/reports/index.php'));
$PAGE->set_pagelayout('report');

require_login();
$context = context_system::instance();
$PAGE->set_context($context);

require_capability('report/log:view', $context);

$PAGE->set_title('Ethos Notifications');

require_once($CFG->libdir.'/weblib.php');

if (!is_siteadmin())
{
    echo "Site Admin privilege required.";
    die();
}

admin_externalpage_setup('reportethosnotifications', '', null, '', array('pagelayout' => 'report'));

echo $OUTPUT->header();
echo "<h2>Last 10 days</h2>";
echo "<p>The table below shows the ethos notification processing summary of the last 10 days.</p>";


$reportService = new ethos_report_service();
$reports = $reportService->getReportSummaries();
if(count($reports) == 0) {
    echo "<p>No reports to display</p>";
    die();
}

?>

<table class="flexible table table-striped table-hover reportlog generaltable generalbox table-sm">
    <thead>
        <tr>
            <th class="header" scope="col">Date</th>
            <th class="header" scope="col">Messages consumed</th>
            <th class="header" scope="col">Messages processed</th>
            <th class="header" scope="col">Users created</th>
            <th class="header" scope="col">Users updated</th>
            <th class="header" scope="col">Elapsed time</th>
        </tr>
    </thead>
    <tbody>
<?php
        foreach($reports as $report) {
            if(!isset($report)) continue;

            $run_date = $report->run_date;
            $messagesConsumed = number_format($report->messages_consumed);
            $messagesProcessed = number_format($report->messages_processed);
            $usersCreated = number_format($report->users_created);
            $usersUpdated = number_format($report->users_updated);
            $elapsedTime = gmdate("H:i:s", $report->elapsed_time);

            echo "<tr>";
                echo "<td class='cell'><a href='{$CFG->wwwroot}/enrol/ethos/reports/detail.php?from=$run_date&to=$run_date'>$run_date</a></td>";
                echo "<td class='cell'>$messagesConsumed</td>";
                echo "<td class='cell'>$messagesProcessed</td>";
                if($usersCreated > 0) {
                    echo "<td class='cell'>$usersCreated</td>";
                }
                else {
                    echo "<td class='cell'>-</td>";
                }
                if($usersUpdated > 0) {
                    echo "<td class='cell'>$usersUpdated</td>";
                }
                else {
                    echo "<td class='cell'>-</td>";
                }
                echo "<td class='cell'>$elapsedTime</td>";
            echo "</tr>";

        }
?>
    </tbody>
</table>

<?php

echo $OUTPUT->footer();