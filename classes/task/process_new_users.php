<?php
namespace enrol_ethos\task;


/**
 * An example of a scheduled task.
 */
class process_new_users extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() : string {
        return "Process new users (where SRS fields are empty)";
    }

    /**
     * Execute the task.
     */
    public function execute() {
        $trace = new text_progress_trace();
        $handler = new \enrol_ethos\handlers\backfill_handler($trace);
        $handler->handleBackFill();
    }
}