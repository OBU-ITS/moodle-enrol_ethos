<?php

namespace enrol_ethos\handlers;

use enrol_ethos\services\mdl_course_service;
use progress_trace;

class sync_module_runs_handler
{
    private mdl_course_service $courseService;

    private progress_trace $trace;

    public function __construct($trace)
    {
        $this->courseService = mdl_course_service::getInstance();

        $this->trace = $trace;
    }

    public function handleSync(string $id)
    {
        $this->courseService->reSyncModuleRun($this->trace, $id);
    }

    public function handleSyncAll(int $max = 0)
    {
        $this->courseService->reSyncAllModuleRuns($this->trace, $max);
    }
}