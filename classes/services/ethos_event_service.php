<?php
namespace enrol_ethos\services;

use enrol_ethos\repositories\db_ethos_report_repository;

class ethos_event_service
{
    private string $currentEventGroup = '';

    private function __construct()
    {
    }

    private static ?ethos_event_service $instance = null;
    public static function getInstance() : ethos_event_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }
}