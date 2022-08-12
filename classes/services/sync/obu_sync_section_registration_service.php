<?php
namespace enrol_ethos\services\sync;

use progress_trace;

class obu_sync_section_registration_service
{
    private function __construct()
    {
    }

    private static ?obu_sync_section_registration_service $instance = null;
    public static function getInstance(): obu_sync_section_registration_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}