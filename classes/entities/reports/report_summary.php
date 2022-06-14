<?php
namespace enrol_ethos\entities\reports;

class report_summary {
    public string $run_date;
    public int $messages_consumed = 0;
    public int $messages_processed= 0;
    public int $users_created= 0;
    public int $users_updated= 0;
    public float $elapsed_time;

    public function __construct($run_date, $messages_consumed, $messages_processed, $users_created, $users_updated, $elapsed_time) {
        $this->run_date = $run_date;
        $this->messages_consumed = $messages_consumed;
        $this->messages_processed = $messages_processed;
        $this->users_created = $users_created;
        $this->users_updated = $users_updated;
        $this->elapsed_time = $elapsed_time;
    }
}