<?php
namespace enrol_ethos\handlers;

use enrol_ethos\ethosclient\services\ethos_notification_service;
use enrol_ethos\processors\base\obu_processor;
use enrol_ethos\services\core_class_finder_service;
use progress_trace;

class ethos_notifications_handler {

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
        core_class_finder_service::includeFilesInFolder("processors");

        foreach(get_declared_classes() as $class) {
            $interfaces = class_implements($class);

            if (!isset($interfaces['enrol_ethos\processors\base\obu_processor']) || !defined($class::RESOURCE_NAME)) {
                continue;
            }

            $instance = new $class($this->trace);
            if ($instance instanceof obu_processor) {
                $resourceName = constant($class::RESOURCE_NAME);
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
        $lastProcessId = 0; // TODO : Get last consumed from ethos audit

        $messages = $this->consumeService->consumeMessages($lastProcessId, $max);
        foreach($messages->getNotificationGroupKeys() as $messageGroupKey) {
            if(!array_key_exists($messageGroupKey, $this->processors)) {
                continue;
            }

            $processor = $this->processors[$messageGroupKey];
            foreach($messages->getNotificationsByResource($messageGroupKey) as $message) {
                $processor->process($message);
            }
        }
    }
}