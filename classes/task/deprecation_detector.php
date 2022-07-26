<?php
namespace enrol_ethos\task;

class deprecation_detector extends \core\task\scheduled_task{

    public function get_name() : string {
        return "deprecation_detector";
    }

    public function execute() {
        $trace = new \text_progress_trace();

        $trace->output("Test 1");
        $trace->output("Test 2");
    }

}