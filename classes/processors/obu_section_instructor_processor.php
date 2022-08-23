<?php
namespace enrol_ethos\processors;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\ethosclient\providers\ethos_section_instructors_provider;
use enrol_ethos\processors\base\obu_processor;
use enrol_ethos\services\sync\obu_sync_section_instructor_service;
use progress_trace;

class obu_section_instructor_processor implements obu_processor {
    const RESOURCE_NAME = ethos_section_instructors_provider::PATH;

    private progress_trace $trace;

    private obu_sync_section_instructor_service $syncSectionInstructorService;

    public function __construct($trace)
    {
        $this->syncSectionInstructorService = obu_sync_section_instructor_service::getInstance();

        $this->trace = $trace;
    }

    function process(ethos_notification $message)
    {
        $this->syncSectionInstructorService->sync($this->trace, $message->id);
    }
}