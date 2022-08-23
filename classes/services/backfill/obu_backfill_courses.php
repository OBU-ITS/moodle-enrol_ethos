<?php
namespace enrol_ethos\services\moodle;

use enrol_ethos\services\sync\obu_sync_academic_program_service;
use enrol_ethos\services\sync\obu_sync_section_service;
use progress_trace;

class obu_backfill_courses
{
    private const RUN_LIMIT = 500;

    private mdl_course_service $courseService;
    private obu_sync_section_service $syncSectionService;
    private obu_sync_academic_program_service $syncAcademicProgramService;

    private function __construct()
    {
        $this->courseService = mdl_course_service::getInstance();
        $this->syncSectionService = obu_sync_section_service::getInstance();
        $this->syncAcademicProgramService = obu_sync_academic_program_service::getInstance();
    }

    private static ?obu_backfill_courses $instance = null;
    public static function getInstance(): obu_backfill_courses
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function backfill(progress_trace $trace) {
        $this->backfill_module_runs($trace);
        $this->backfill_academic_programs($trace);
    }

    public function backfill_module_runs(progress_trace $trace, $max = 0) {
        $offset = 0;
        $totalResults = 0;

        do {
            $limit = ($max && ($max < ($totalResults + self::RUN_LIMIT))) ? ($max - $totalResults) : self::RUN_LIMIT;

            $courses = $this->courseService->getModuleRuns($limit, $offset);
            foreach($courses as $course) {
                $this->syncSectionService->syncByCourse($trace, $course);
            }

            $offset += self::RUN_LIMIT;
            $resultsCount = count($courses);
            $totalResults += $resultsCount;
        }
        while($resultsCount > 0 && ($max == 0 || ($max > $totalResults)));
    }

    public function backfill_academic_programs(progress_trace $trace, $max = 0) {
        $offset = 0;
        $totalResults = 0;

        do {
            $limit = ($max && ($max < ($totalResults + self::RUN_LIMIT))) ? ($max - $totalResults) : self::RUN_LIMIT;

            $courses = $this->courseService->getPrograms($limit, $offset);
            foreach($courses as $course) {
                $this->syncAcademicProgramService->syncByCourse($trace, $course);
            }

            $offset += self::RUN_LIMIT;
            $resultsCount = count($courses);
            $totalResults += $resultsCount;
        }
        while($resultsCount > 0 && ($max == 0 || ($max > $totalResults)));
    }
}
