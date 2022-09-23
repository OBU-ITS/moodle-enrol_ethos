<?php
namespace enrol_ethos\processors;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\ethosclient\providers\ethos_person_hold_provider;
use enrol_ethos\processors\base\obu_processor;
use enrol_ethos\services\sync\obu_sync_person_hold_service;
use progress_trace;

class obu_person_hold_processor implements obu_processor {
    const RESOURCE_NAME = ethos_person_hold_provider::PATH;

    private obu_sync_person_hold_service $syncService;

    private progress_trace $trace;

    public function __construct($trace) {
        $this->syncService = obu_sync_person_hold_service::getInstance();

        $this->trace = $trace;
    }

    function process(ethos_notification $message) {
        $this->trace->output("Processing Person hold ($message->resourceId)");

        if($message->operation == "deleted") {
            $this->syncService->remove($this->trace, $message->resourceId);
        }
        else {
            $this->syncService->sync($this->trace, $message->resourceId);
        }
    }
}