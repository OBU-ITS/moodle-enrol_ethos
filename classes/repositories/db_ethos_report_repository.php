<?php
namespace enrol_ethos\repositories;

use enrol_ethos\entities\reports\report_run;
use enrol_ethos\entities\reports\report_action;

class db_ethos_report_repository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createReportRun(report_run $report) : int {
        return $this->db->insert_record('ethos_report_run', $report);
    }

    public function createReportAction(report_action $action) {
        $this->db->insert_record('ethos_report_action', $action);
    }

    public function getReportRuns(int $from, int $to): array
    {
        $sql = "SELECT * FROM {ethos_report_run} WHERE run_time >= :from AND run_time < :to";

        $items = $this->db->get_records_sql($sql, ["from" => $from, "to" => $to]);
        return $this->mapToReportRuns($items);
    }

    public function getLastConsumedId() : int {
        $sql = "SELECT last_consumed_id FROM {ethos_report_run} ORDER BY last_consumed_id DESC LIMIT 1";

        $result = $this->db->get_records_sql($sql);

        return array_key_first($result) ?? 0;
    }

    private function mapToReportRuns($items) : array {
        $result = array();

        if(!isset($items)) {
            return $result;
        }

        foreach($items as $item){
            $message = $this->mapToReportRun($item);

            if(!is_null($message)) {
                $result[] = $message;
            }
        }

        return $result;
    }

    private function mapToReportRun($item) : ?report_run {
        if ($item) {


            $report = new report_run();

            $report->id = $item->id;
            $report->run_time = $item->run_time;
            $report->messages_consumed = $item->messages_consumed;
            $report->messages_processed = $item->messages_processed;
            $report->users_created = $item->users_created;
            $report->users_updated = $item->users_updated;
            $report->elapsed_time = $item->elapsed_time;
            $report->last_consumed_id = $item->last_consumed_id;

            return $report;
        }

        return null;
    }
}