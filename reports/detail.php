<?php

use enrol_ethos\entities\report_run;
use enrol_ethos\services\ethos_report_service;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
$PAGE->set_url(new moodle_url('/enrol/ethos/reports/detail.php'));
$PAGE->set_pagelayout('report');

require_login();
$context = context_system::instance();
$PAGE->set_context($context);

require_capability('report/log:view', $context);

$PAGE->set_title('Ethos Notification run report');

require_once($CFG->libdir.'/weblib.php');

if (!is_siteadmin())
{
    echo "Site Admin privilege required.";
    die();
}

$fromQs = $_GET["from"] ?? date("y-m-d 00:00:00");
$from = strtotime($fromQs);
$fromStr = date('Y-m-d h:i:sa', $from);

$toQs = $_GET["to"] ?? date("y-m-d 00:00:00");
$to = strtotime($toQs . " + 1 day");
$toStr = date("Y-m-d h:i:sa", $to);

admin_externalpage_setup('reportethosnotifications', '', null, '', array('pagelayout' => 'report'));

echo $OUTPUT->header();
echo "<h2>$fromStr - $toStr</h2>";

$reportService = new ethos_report_service();
$reports = $reportService->getReports($from, $to);
if(count($reports) == 0) {
    echo "<p>No reports to display</p>";
    die();
}

$totalMessagesConsumed = 0;
$totalMessagesProcessed = 0;
$totalUsersCreated = 0;
$totalUsersUpdated = 0;
$totalElapsedTime = 0;

?>

    <table class="flexible table table-striped table-hover reportlog generaltable generalbox table-sm">
        <thead>
        <tr>
            <th class="header" scope="col">Run time</th>
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

            $run_time = date('H:i:sa', (int)$report->run_time);
            $messagesConsumed = number_format($report->messages_consumed);
            $messagesProcessed = number_format($report->messages_processed);
            $usersCreated = number_format($report->users_created);
            $usersUpdated = number_format($report->users_updated);
            $elapsedTime = gmdate("H:i:s", $report->elapsed_time);

            echo "<tr>";
            echo "<td class='cell'>$run_time</td>";
            echo "<td class='cell'>$messagesConsumed</td>";
            echo "<td class='cell'>$messagesProcessed</td>";
            if($usersCreated > 0) {
                echo "<td class='cell'><a href='{$CFG->wwwroot}/enrol/ethos/reports/changes.php?id=$report->id#userscreated'>$usersCreated</a></td>";
            }
            else {
                echo "<td class='cell'>-</td>";
            }
            if($usersUpdated > 0) {
                echo "<td class='cell'><a href='{$CFG->wwwroot}/enrol/ethos/reports/changes.php?id=$report->id#usersupdated'>$usersUpdated</a></td>";
            }
            else {
                echo "<td class='cell'>-</td>";
            }
            echo "<td class='cell'>$elapsedTime</td>";
            echo "</tr>";

            $totalMessagesConsumed += $report->messages_consumed;
            $totalMessagesProcessed += $report->messages_processed;
            $totalUsersCreated += $report->users_created;
            $totalUsersUpdated += $report->users_updated;
            $totalElapsedTime += $report->elapsed_time;
        }
        ?>
        </tbody>
        <tfoot style="border-top: solid 2px black">
        <tr>
            <td><strong>TOTALS</strong></td>
            <td><strong><?php echo number_format($totalMessagesConsumed) ?></strong></td>
            <td><strong><?php echo number_format($totalMessagesProcessed) ?></strong></td>
            <td><strong><?php echo number_format($totalUsersCreated) ?></strong></td>
            <td><strong><?php echo number_format($totalUsersUpdated) ?></strong></td>
            <td><strong><?php echo gmdate("H:i:s", $totalElapsedTime) ?></strong></td>
        </tr>
        </tfoot>
    </table>

<?php

echo $OUTPUT->footer();