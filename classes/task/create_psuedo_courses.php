<?php
namespace enrol_ethos\task;

defined('MOODLE_INTERNAL') || die;

/**
 * An example of a scheduled task.
 */
class create_psuedo_courses extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return "Create psuedo courses";
    }

    /**
     * Execute the task.
     */
    public function execute() {
        $trace = new text_progress_trace();

        $processingService= new \enrol_ethos\services\processing_service($trace);
        $processingService->create_psuedo_courses();
    }
}