<?php
namespace enrol_ethos\processors;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\processors\base\obu_processor;
use enrol_ethos\services\sync\obu_sync_academic_program_service;
use progress_trace;

class obu_academic_program_processor implements obu_processor {
    const RESOURCE_NAME = "academic-programs";

    private obu_sync_academic_program_service $syncService;

    private progress_trace $trace;

    public function __construct($trace)
    {
        $this->syncService = obu_sync_academic_program_service::getInstance();

        $this->trace = $trace;
    }

    function process(ethos_notification $message)
    {
        $this->syncService->sync($this->trace, $message->id);
    }
}