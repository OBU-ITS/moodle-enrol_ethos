<?php

namespace enrol_ethos\handlers;

use enrol_ethos\services\mdl_course_service;

class sync_module_runs_handler
{
    private mdl_course_service $courseService;

    public function __construct()
    {
        $this->courseService = mdl_course_service::getInstance();
    }

    public function handleSync(string $id)
    {
        $this->courseService->reSyncModuleRun($id);
    }

    public function handleSyncAll(int $max = 0)
    {
        $this->courseService->reSyncAllModuleRuns($max);
    }
}