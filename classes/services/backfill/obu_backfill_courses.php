<?php
namespace enrol_ethos\services\moodle;

use progress_trace;

class obu_backfill_courses
{

    private function __construct()
    {
    }

    private static ?obu_backfill_courses $instance = null;
    public static function getInstance(): obu_backfill_courses
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function backfill() {

    }
}
