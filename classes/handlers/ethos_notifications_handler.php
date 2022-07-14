<?php
namespace enrol_ethos\handlers;

use enrol_ethos\entities\reports\report_action;
use enrol_ethos\entities\reports\report_run;
use enrol_ethos\ethosclient\client\ethos_client;
use enrol_ethos\ethosclient\service\message_model;
use enrol_ethos\ethosclient\service\messages_model;
use enrol_ethos\services\ethos_report_service;
use enrol_ethos\services\processing_service;

class ethos_notifications_handler {
    private ethos_report_service $reportService;
    private processing_service $processingService;
    private ethos_client $ethosClient;

    private const PROCESS_LIMIT = 2000;

    public function __construct($trace)
    {
        $this->reportService = new ethos_report_service();
        $this->processingService = new processing_service($trace);
        $this->ethosClient = ethos_client::getInstance();
    }

    public function handleNotifications() {
        $reportRun = new report_run();

        $reportActions = $this->processNotifications($reportRun);

        $this->reportService->saveReport($reportRun, $reportActions);
    }

    /**
     * @param report_run $reportRun The report run
     * @return report_action[] The actions created in the report run
     */
    private function processNotifications(report_run $reportRun) : array {
        $messages = $this->getNotifications($reportRun);

        if($messages->isEmpty()){
            return array();
        }

        return $this->processingService->process_ethos_updates($reportRun, $messages);
    }

    private function getNotifications(report_run $report): messages_model
    {
        $messagesModel = new messages_model();

        $lastConsumedID = $this->reportService->getLastConsumedId(Id);
        $processedCount = 0;

        do {
            $messages = $this->ethosClient->consumeMessages($lastConsumedID);

            $messagesCount = count($messages);
            $report->incrementMessagesConsumed($messagesCount);

            foreach ($messages as $message) {

                $lastConsumedID = $message->id;

                $processedCount++;

                if (isset($message->resource)
                    && isset($message->content)
                    && isset($message->operation)
                    && ($message->operation !== 'deleted')) {

                    $messageId = $message->id;
                    $resourceName = $message->resource->name;
                    $resourceId = $message->resource->id;
                    $messageContent = $message->content;

                    switch ($resourceName) {
                        case 'persons':
                            $messageModel = new message_model($messageId, $resourceId, $messageContent->id);

                            if($messagesModel->addPerson($messageModel)) {
                                $report->incrementMessagesProcessed();
                            }
                            break;
                        case 'student-academic-programs':
                            $messageModel = new message_model($messageId, $resourceId, $messageContent->student->id);

                            if($messagesModel->addStudentAcademicPrograms($messageModel)) {
                                $report->incrementMessagesProcessed();
                            }
                            break;
                    }
                }
            }
        } while ($messagesCount > 0 && $processedCount < self::PROCESS_LIMIT);

        $report->last_consumed_id = $lastConsumedID;

        return $messagesModel;
    }
}