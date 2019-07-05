<?php
namespace enrol_ethos\task;
 
// require 'client/EthosClient.php';
// require 'service/StudentLookupService.php';

/**
 * An example of a scheduled task.
 */
class process_ethos_updates extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return "Process Ethos updates";
    }
 
    /**
     * Execute the task.
     */
    public function execute() {
        $trace = new text_progress_trace();
        
        $processingService= new \enrol_ethos\services\processing_service($trace);
        $processingService->process_ethos_updates();
    }
}