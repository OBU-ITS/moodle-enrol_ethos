<?php
namespace enrol_ethos\processors;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\ethosclient\providers\ethos_section_provider;
use enrol_ethos\processors\base\obu_processor;
use enrol_ethos\services\sync\obu_sync_section_service;
use progress_trace;

class obu_section_processor implements obu_processor {
    const RESOURCE_NAME = ethos_section_provider::PATH;

    private obu_sync_section_service $syncService;

    private progress_trace $trace;

    public function __construct($trace)
    {
        $this->syncService = obu_sync_section_service::getInstance();

        $this->trace = $trace;
    }

    function process(ethos_notification $message)
    {
        $this->syncService->sync($this->trace, $message->resourceId);
    }
}