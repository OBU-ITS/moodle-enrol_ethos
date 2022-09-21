<?php
namespace enrol_ethos\handlers;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\ethosclient\entities\consume\ethos_notifications;
use enrol_ethos\ethosclient\services\ethos_notification_service;
use enrol_ethos\helpers\core_class_finder_helper;
use enrol_ethos\processors\base\obu_processor;
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
        $lastProcessId = 0; // TODO : Get last consumed from ethos audit

        $messages = isset($max)
            ? $this->consumeService->consumeMessages($lastProcessId, $max)
            : $this->consumeService->consumeMessages($lastProcessId);

        foreach($messages->getNotificationGroupKeys() as $messageGroupKey) {
            $this->processNotificationGroup($messageGroupKey, $messages);
        }
    }

    private function processNotificationGroup(string $messageGroupKey, ethos_notifications $messages) {
        $this->trace->output("Searching processor for $messageGroupKey");

        if(!array_key_exists($messageGroupKey, $this->processors)) {
            $this->trace->output("Skipping $messageGroupKey");
            return;
        }

        $processor = $this->processors[$messageGroupKey];
        $this->trace->output("Processor found $messageGroupKey");
        foreach($messages->getNotificationsByResource($messageGroupKey) as $message) {
            $this->processNotificationResource($processor, $message);
        }
    }

    private function processNotificationResource(obu_processor $processor, ethos_notification $message) {
        $this->trace->output("Processing $message->resourceId");
        $processor->process($message);
    }
}