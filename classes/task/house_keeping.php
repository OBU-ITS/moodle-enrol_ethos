<?php
namespace enrol_ethos\task;

use enrol_ethos\handlers\house_keeping_handler;
use text_progress_trace;

class house_keeping extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name(): string
    {
        return "Data house keeping";
    }

    /**
     * Execute the task.
     */
    public function execute() {
        $trace = new text_progress_trace();
        $handler = new house_keeping_handler($trace);
        $handler->handlePersonHoldsCleaning();
    }
}