<?php
namespace enrol_ethos\services\sync;

use progress_trace;

class obu_sync_student_advisor_service
{
    private function __construct()
    {
    }

    private static ?obu_sync_student_advisor_service $instance = null;
    public static function getInstance(): obu_sync_student_advisor_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sync(progress_trace $trace, $id)
    {
        // Get person (student) from student_advisor
        // Get person (advisor) from student_advisor

        // update both records
    }
}