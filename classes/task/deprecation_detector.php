<?php
namespace enrol_ethos\task;

class deprecation_detector extends \core\task\scheduled_task{

    public function get_name() : string {
        return "Deprecation Detector";
    }

    public function execute() {
        $trace = new \text_progress_trace();

        $handler = new \enrol_ethos\handlers\deprecation_detector_handler($trace);
        $handler->handleDetectingDeprecations();
    }
}