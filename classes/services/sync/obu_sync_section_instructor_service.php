<?php

namespace enrol_ethos\services\sync;

use progress_trace;

class obu_sync_section_instructor_service
{
    private function __construct()
    {
    }

    private static ?obu_sync_section_instructor_service $instance = null;
    public static function getInstance(): obu_sync_section_instructor_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sync(progress_trace $trace, $id)
    {
        // get person (instructor) from the section_instructor record

        // update record
    }
}