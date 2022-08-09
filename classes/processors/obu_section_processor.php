<?php
namespace enrol_ethos\processors;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\processors\base\obu_processor;
use enrol_ethos\services\mdl_course_service;
use progress_trace;

class obu_section_processor implements obu_processor {
    const RESOURCE_NAME = "sections";

    private mdl_course_service $courseService;

    private progress_trace $trace;

    public function __construct($trace)
    {
        $this->courseService = mdl_course_service::getInstance();

        $this->trace = $trace;
    }

    function process(ethos_notification $message)
    {
        $this->courseService->reSyncModuleRun($this->trace, $message->id);
    }
}