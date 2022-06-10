<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\reports\report_action;
use enrol_ethos\repositories\db_ethos_report_repository;
use enrol_ethos\entities\reports\report_run;

class ethos_report_service
{
    private db_ethos_report_repository $reportRepository;

    public function __construct()
    {
        global $DB;

        $this->reportRepository = new db_ethos_report_repository($DB);
    }

    /**
     * @param int $from
     * @param int $to
     * @return report_run[]
     */
    public function getReports(int $from, int $to) : array {
        return $this->reportRepository->getReportRuns($from, $to);
    }

    /**
     * @param report_run $report
     * @param report_action[] $actions
     */
    public function saveReport(report_run $report, array $actions) {
        if(!isset($report)) {
            return;
        }

        if($report->messages_consumed < 1) {
            return;
        }

        $report->finish();

        $reportId = $this->reportRepository->createReportRun($report);

        foreach($actions as $action) {
            $action->run_id = $reportId;
            $this->reportRepository->createReportAction($action);
        }
    }

    /**
     * @return int
     */
    public function getLastConsumedId() : int {
        return $this->reportRepository->getLastConsumedId();
    }
}