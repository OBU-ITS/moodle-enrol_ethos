<?php
namespace enrol_ethos\ethosclient\services;

use enrol_ethos\ethosclient\client\ethos_client;
use enrol_ethos\ethosclient\consumers\base\ethos_consumer;
use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\ethosclient\entities\consume\ethos_notifications;
use enrol_ethos\ethosclient\general\class_finder;
use enrol_ethos\ethosclient\repositories\db_ethos_audit_repository;
use Exception;
use progress_trace;

class ethos_notification_service
{
    public const CONSUME_LIMIT = 250;

    private ethos_client $ethosClient;
    private db_ethos_audit_repository $ethosAuditRepo;

    private function __construct()
    {
        $this->ethosClient = ethos_client::getInstance();
        $this->ethosAuditRepo = db_ethos_audit_repository::getInstance();
    }

    private static ?ethos_notification_service $instance = null;
    public static function getInstance() : ethos_notification_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Consume Messages
     *
     * @param string $lastProcessedId
     * @param int $limit
     * @return ethos_notifications
     */
    public function consumeMessages(progress_trace $trace, string $lastProcessedId = "0", int $limit = self::CONSUME_LIMIT) : ethos_notifications {

        $recordRequest = $this->ethosAuditRepo->createRecordRequest();
        $notifications = new ethos_notifications();

        $url = ethos_client::API_URL . "/consume?limit=". $limit ."&lastProcessedID=" . $lastProcessedId;
        $trace->output("ETHOS REQUEST: $url");

        try {
            $ethosResponse = $this->ethosClient->getJson($url, "");
            $messages = $ethosResponse->messages;
            $recordRequest->received_count = count($messages);
            $recordRequest->remaining_count = $lastProcessedId; // TEMPORARY
        }
        catch(Exception $e) {
            $this->ethosAuditRepo->updateRecordRequestAsFailed($recordRequest);
            return $notifications;
        }

        foreach ($messages as $message) {
            $this->ethosAuditRepo->createRecord($trace, $recordRequest, $message);

            $notification = new ethos_notification();
            $notification->populateObject($message);
            $notifications->addNotification($notification);
        }

//        if($ethosResponse->remainingCount == 0) {
            $this->ethosAuditRepo->updateRecordRequestAsDone($recordRequest);
//        }
//        else {
//            $this->ethosAuditRepo->updateRecordRequestAsComplete($recordRequest);
//        }

        return $notifications;
    }
}
