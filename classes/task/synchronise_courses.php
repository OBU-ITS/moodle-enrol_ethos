<?php
namespace enrol_ethos\task;

use enrol_ethos\handlers\sync_module_runs_handler;
use text_progress_trace;

/**
 * An example of a scheduled task.
 */
class synchronise_courses extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() : string {
        return "Synchronise courses with Ethos";
    }

    /**
     * Execute the task.
     */
    public function execute() {
        ini_set('max_execution_time', 3600);

        $trace = new text_progress_trace();
        $handler = new sync_module_runs_handler($trace);
        $handler->handleSyncAll(500);
    }
}