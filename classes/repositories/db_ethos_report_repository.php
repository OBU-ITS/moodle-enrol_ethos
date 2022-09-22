<?php
namespace enrol_ethos\repositories;

use enrol_ethos\entities\reports\report_run;
use enrol_ethos\entities\reports\report_action;
use enrol_ethos\entities\reports\report_summary;

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

    public function getReportActions($id, $resource, $action) : array {
        $sql = "SELECT * FROM {ethos_report_action} WHERE run_id = :id AND resource_name = :resource AND action_type = :action";

        $items = $this->db->get_records_sql($sql, ["id" => $id, "resource" => $resource, "action" => $action]);
        return $this->mapToReportActions($items);
    }

    public function getReportRuns(int $from, int $to): array
    {
        $sql = "SELECT * FROM {ethos_report_run} WHERE run_time >= :from AND run_time < :to";

        $items = $this->db->get_records_sql($sql, ["from" => $from, "to" => $to]);
        return $this->mapToReportRuns($items);
    }

    public function getReportSummaries(): array
    {
        $sql = "SELECT DATE(FROM_UNIXTIME(run_time)) run_date, SUM(messages_consumed) messages_consumed, SUM(messages_processed) messages_processed, SUM(users_created) users_created, SUM(users_updated) users_updated, sum(elapsed_time) elapsed_time FROM {ethos_report_run} GROUP BY run_date ORDER BY run_date DESC LIMIT 10";

        $items = $this->db->get_records_sql($sql);

        return $this->mapToReportSummaries($items);
    }

    public function getLastConsumedId() : int {
        $sql = "SELECT last_consumed_id FROM {ethos_report_run} ORDER BY last_consumed_id DESC LIMIT 1";

        $result = $this->db->get_records_sql($sql);

        return array_key_first($result) ?? 0;
    }

    private function mapToReportActions($items) : array {
        $result = array();

        if(!isset($items)) {
            return $result;
        }

        foreach($items as $item){
            $message = $this->mapToReportAction($item);

            if(!is_null($message)) {
                $result[] = $message;
            }
        }

        return $result;
    }

    private function mapToReportAction($item) : ?report_action {
        if ($item) {

            $report = new report_action($item->action_type, $item->resource_name, $item->resource_id, $item->resource_description);

            $report->run_id = $item->run_id;

            return $report;
        }

        return null;
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

    private function mapToReportSummaries($items) : array {
        $result = array();

        if(!isset($items)) {
            return $result;
        }

        foreach($items as $item){
            $message = $this->mapToReportSummary($item);

            if(!is_null($message)) {
                $result[] = $message;
            }
        }

        return $result;
    }

    private function mapToReportSummary($item) : ?report_summary {
        return $item
            ? new report_summary($item->run_date, $item->messages_consumed, $item->messages_processed, $item->users_created, $item->users_updated, $item->elapsed_time)
            : null;
    }
}