<?php
namespace enrol_ethos\ethosclient\services;

use enrol_ethos\ethosclient\client\ethos_client;
use enrol_ethos\ethosclient\consumers\base\ethos_consumer;
use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\ethosclient\entities\consume\ethos_notifications;
use enrol_ethos\ethosclient\general\class_finder;
use Exception;

class ethos_notification_service
{
    private const CONSUME_LIMIT = 250;
    private const PROCESS_LIMIT = 2000;

    private ethos_client $ethosClient;

    private function __construct()
    {
        $this->ethosClient = ethos_client::getInstance();
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
     * @param string $previousLastProcessedId
     * @param int $max
     * @return ethos_notifications
     */
    public function consumeMessages(string $previousLastProcessedId = "0", int $max = self::PROCESS_LIMIT) : ethos_notifications {

        $notifications = new ethos_notifications();
        $lastProcessedId = $previousLastProcessedId;
        $processedCount = 0;

        do {
            $limit = ($max && ($max < ($processedCount + self::CONSUME_LIMIT)))
                ? ($max - $processedCount)
                : self::CONSUME_LIMIT;
            $url = ethos_client::API_URL . "/consume?limit=". $limit ."&lastProcessedID=" . $lastProcessedId;

            try {
                $messages = $this->ethosClient->getJson($url, null);
            }
            catch(Exception $e) {
                breaK;
            }

            $resultsCount = count($messages);

            foreach ($messages as $message) {
                $lastProcessedId = $message->id;
                $notification = new ethos_notification($messages);
                $notifications->addNotification($notification);
            }

            $processedCount += $resultsCount;
        }
        while ($resultsCount > 0 && self::PROCESS_LIMIT > $processedCount);

        return $notifications;
    }
}
