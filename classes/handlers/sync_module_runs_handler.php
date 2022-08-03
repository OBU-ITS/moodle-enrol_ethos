<?php

namespace enrol_ethos\handlers;

use enrol_ethos\services\mdl_course_service;
use progress_trace;

class sync_module_runs_handler
{
    private mdl_course_service $courseService;

    public function __construct()
    {
        $this->courseService = mdl_course_service::getInstance();
    }

    public function handleSync(progress_trace $trace, string $id)
    {
        $this->courseService->reSyncModuleRun($trace, $id);
    }

    public function handleSyncAll(progress_trace $trace, int $max = 0)
    {
        $this->courseService->reSyncAllModuleRuns($trace, $max);
    }
}