<?php
namespace enrol_ethos\task;


/**
 * An example of a scheduled task.
 */
class process_ethos_updates extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name(): string
    {
        return "Process Ethos Notifications";
    }

    /**
     * Execute the task.
     */
    public function execute() {
        $trace = new \text_progress_trace();
        $handler = new \enrol_ethos\handlers\ethos_notifications_handler($trace);
        $handler->handleNotifications();
    }
}