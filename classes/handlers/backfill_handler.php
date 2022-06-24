<?php
namespace enrol_ethos\handlers;


use enrol_ethos\services\backfill_service;

class backfill_handler
{
    private backfill_service $backfillService;

    public function __construct($trace)
    {
        $this->backfillService = new backfill_service($trace);
    }

    public function handleBackFill() {
        $this->backfillService->backfillUserBannerGuids();
    }
}