<?php
namespace enrol_ethos\ethosclient\repositories;

use enrol_ethos\ethosclient\entities\request\ethos_request;
use Matrix\Exception;
use progress_trace;

class db_ethos_audit_repository
{
    private $db;

    private function __construct()
    {
        global $DB;

        $this->db = $DB;
    }


    private static ?db_ethos_audit_repository $instance = null;
    public static function getInstance() : db_ethos_audit_repository
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return ethos_request|null
     */
    public function createRecordRequest() : ?ethos_request {
        $request = new \stdClass();
        $request->requested = time();
        $request->request_duration = 0;
        $request->status = "PENDING";
        $request->received_count = 0;
        $request->remaining_count = 0;

        try {
            $id = $this->db->insert_record('obu_ethos_request', $request);
        }
        catch (\moodle_exception $exception) {
            return null;
        }

        $ethosRequest = new ethos_request();
        $ethosRequest->id = $id;
        $ethosRequest->requested = $request->requested;
        $ethosRequest->request_duration = $request->request_duration;
        $ethosRequest->status = $request->status;
        $ethosRequest->received_count = $request->received_count;
        $ethosRequest->remaining_count = $request->remaining_count;

        return $ethosRequest;
    }

    public function updateRecordRequestAsComplete(ethos_request $ethosRequest) {
        $this->completeRequest($ethosRequest, "COMPLETE");
    }

    public function updateRecordRequestAsFailed(ethos_request $ethosRequest) {
        $this->completeRequest($ethosRequest, "FAILED");
    }

    public function updateRecordRequestAsDone(ethos_request $ethosRequest) {
        $this->completeRequest($ethosRequest, "DONE");
    }

    private function completeRequest(ethos_request $ethosRequest, string $status) {
        $ethosRequest->request_duration = time() - $ethosRequest->requested;
        $ethosRequest->status = $status;

        try {
            $this->db->update_record('obu_ethos_request', $ethosRequest);
        }
        catch (\moodle_exception $exception) {
            return;
        }
    }

    public function createRecord(progress_trace $trace, ethos_request $ethosRequest, object $message) {
        try {
            $existingRecord = $this->db->get_record('obu_ethos_message', ['id' => $message->id]);
            if(!$existingRecord) {
                $sql  = 'INSERT INTO {obu_ethos_message} (id,request_id,published,resource_name,resource_id,operation,content_type,content) VALUES(?,?,?,?,?,?,?,?)';
                $this->db->execute($sql, [
                    'id' => $message->id,
                    'request_id' => $ethosRequest->id,
                    'published' => $message->published,
                    'resource_name' => $message->resource->name,
                    'resource_id' => $message->resource->id,
                    'operation' => $message->operation,
                    'content_type' => $message->contentType,
                    'content' => \GuzzleHttp\json_encode($message->content),
                ]);
            }
        }
        catch (\moodle_exception $exception) {
            $trace->output($exception->getMessage());
            return null;
        }
    }

    private function messageToRecord(object $message, int $requestId) : object {
        $record = new \stdClass();

        $record->id = $message->id;
        $record->request_id = $requestId;
        $record->published = $message->published;
        $record->resource_name = $message->resource->name;
        $record->resource_id = $message->resource->id;
        $record->operation = $message->operation;
        $record->content_type = $message->contentType;
        $record->content = \GuzzleHttp\json_encode($message->content);

        return $record;
    }
}