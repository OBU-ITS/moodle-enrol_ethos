<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\repositories\db_ethos_audit_repository;

class ethos_audit_service
{
    private db_ethos_audit_repository $ethosAuditRepo;

    private function __construct()
    {
        $this->ethosAuditRepo = db_ethos_audit_repository::getInstance();
    }

    private static ?ethos_audit_service $instance = null;
    public static function getInstance() : ethos_audit_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function recordNotification(ethos_notification $message)
    {
        $this->ethosAuditRepo->createRecord($message);
    }
}