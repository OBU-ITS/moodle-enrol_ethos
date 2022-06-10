<?php
namespace enrol_ethos\entities\reports;

class report_run {
    public int $id;
    public float $run_time;
    public int $messages_consumed = 0;
    public int $messages_processed= 0;
    public int $users_created= 0;
    public int $users_updated= 0;
    public float $elapsed_time;
    public int $last_consumed_id;

    public function __construct()
    {
        $this->run_time = microtime(true);
    }

    public function incrementMessagesConsumed(int $count = 1) {
        $this->messages_consumed = $this->messagesConsumed + $count;
    }

    public function incrementMessagesProcessed() {
        $this->messages_processed++;
    }

    public function incrementUsersCreated() {
        $this->users_created++;
    }

    public function incrementUsersUpdated() {
        $this->users_updated++;
    }

    public function finish() {
        $runEndTime = microtime(true);
        $this->elapsed_time = round($runEndTime - $this->run_time, 2, PHP_ROUND_HALF_UP);
    }
}