<?php
namespace enrol_ethos\services\sync;

use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\services\moodle\mdl_course_service;
use enrol_ethos\services\obu_module_run_service;
use progress_trace;

class obu_sync_section_service
{
    private const RUN_LIMIT = 100;

    private obu_module_run_service $moduleRunService;
    private mdl_course_service $courseService;

    private function __construct()
    {
        $this->moduleRunService = obu_module_run_service::getInstance();
        $this->courseService = mdl_course_service::getInstance();
    }

    private static ?obu_sync_section_service $instance = null;
    public static function getInstance(): obu_sync_section_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sync(progress_trace $trace, $id)
    {
        $hierarchy = obu_course_hierarchy_info::getTopCategory();

        $trace->output("Start re-sync module run for id:" . $id);

        $this->moduleRunService->get($hierarchy, $id);

        $this->courseService->handleCourseCreation($trace, $hierarchy);
    }

    public function syncAll(progress_trace $trace, int $max = 0) {
        $offset = 0;
        $totalResults = 0;

        do {
            $hierarchy = obu_course_hierarchy_info::getTopCategory();

            $limit = ($max && ($max < ($totalResults + self::RUN_LIMIT))) ? ($max - $totalResults) : self::RUN_LIMIT;
            $resultsCount = $this->moduleRunService->getBatch($hierarchy, $limit, $offset);

            $this->courseService->handleCourseCreation($trace, $hierarchy);

            $offset += self::RUN_LIMIT;
            $totalResults += $resultsCount;
        }
        while($resultsCount > 0 && ($max == 0 || ($max > $totalResults)));
    }
}
