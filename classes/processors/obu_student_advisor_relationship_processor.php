<?php
namespace enrol_ethos\processors;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\ethosclient\providers\ethos_student_advisor_relationship_provider;
use enrol_ethos\processors\base\obu_processor;
use enrol_ethos\services\sync\obu_sync_student_advisor_service;
use progress_trace;

class obu_student_advisor_relationship_processor implements obu_processor {
    const RESOURCE_NAME = ethos_student_advisor_relationship_provider::PATH;

    private obu_sync_student_advisor_service $syncStudentAdvisorService;

    private progress_trace $trace;

    public function __construct($trace)
    {
        $this->syncStudentAdvisorService = obu_sync_student_advisor_service::getInstance();

        $this->trace = $trace;
    }

    function process(ethos_notification $message)
    {
        $this->syncStudentAdvisorService->sync($this->trace, $message->id);
    }
}