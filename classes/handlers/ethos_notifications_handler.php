<?php
namespace enrol_ethos\handlers;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\ethosclient\services\ethos_notification_service;
use enrol_ethos\helpers\core_class_finder_helper;
use enrol_ethos\processors\base\obu_processor;
use progress_trace;

class ethos_notifications_handler {

    private const PROCESS_LIMIT = 2001;

    private ethos_notification_service $consumeService;

    /**
     * @var obu_processor[]
     */
    private array $processors;

    private progress_trace $trace;

    public function __construct($trace)
    {
        $this->consumeService = ethos_notification_service::getInstance();

        $this->trace = $trace;
        $this->populateProcessors();
    }

    private function populateProcessors() {
        $this->processors = array();
        core_class_finder_helper::includeFilesInFolder("processors");

        foreach(get_declared_classes() as $class) {
            $interfaces = class_implements($class);

            if (!isset($interfaces['enrol_ethos\processors\base\obu_processor'])) {
                continue;
            }

            $instance = new $class($this->trace);
            if ($instance instanceof obu_processor) {
                try {
                    $constant_reflex = new \ReflectionClassConstant($class, 'RESOURCE_NAME');
                    $resourceName = $constant_reflex->getValue();
                } catch (\ReflectionException $e) {
                    $resourceName = '';
                }
                $this->processors[$resourceName] = $instance;
            }
        }
    }

    /**
     * @param int|null $max Maximum number of messages to consume
     */
    public function handleNotifications(?int $max = null) {
        //$reportRun = new report_run();

        $this->processNotifications($max); //$reportActions = $this->processNotifications();

        //$this->reportService->saveReport($reportRun, $reportActions);
    }

    private function processNotifications(?int $max) {
        $processLimit = $max ?? self::PROCESS_LIMIT;

        $lastProcessId = 0;
        $processedCount = 0;

        do {
            $limit = $processLimit < ($processedCount + ethos_notification_service::CONSUME_LIMIT)
                ? ($processLimit - $processedCount)
                : ethos_notification_service::CONSUME_LIMIT;

            $notifications = $this->consumeService->consumeMessages($lastProcessId, $limit);
            $resultsCount = $notifications->getRetrievedCount();

            foreach($notifications->getNotifications() as $notification) {
                try {
                    $this->processNotification($notification);
                }
                catch (\Exception $e) {
                    return;
                }

                $lastProcessId = $notification->id;
            }

            $processedCount += $resultsCount;
        }
        while ($resultsCount > 0 && $processLimit > $processedCount);
    }

    private function processNotification(ethos_notification $message) {
        if(!array_key_exists($message->resourceName, $this->processors)) {
            $this->trace->output("No Processor found for $message->resourceName");
            return;
        }

        $processor = $this->processors[$message->resourceName];

        $this->processNotificationResource($processor, $message);
    }

    private function processNotificationResource(obu_processor $processor, ethos_notification $message) {
        $processor->process($message);
    }
}