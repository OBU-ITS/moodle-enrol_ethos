<?php
namespace enrol_ethos\task;

/**
 * An example of a scheduled task.
 */
class test_scheduled_task extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() : string {
        return "Test";
    }

    /**
     * Execute the task.
     */
    public function execute() {
        $trace = new \text_progress_trace();

        $trace->output("Test 1");
        $trace->output("Test 2");
    }
}