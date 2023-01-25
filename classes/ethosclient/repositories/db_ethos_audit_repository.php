<?php
namespace enrol_ethos\ethosclient\repositories;

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

    public function createRecordRequest(int $lastProcessedID, int $limit) : int {
        // TODO : table which records the request for a collection of messages
        // Start timestamp
        // Default status as success

        return 1;
    }

    public function updateRecordRequestElapsedTime(int $id, int $time) {
        // TODO : log
    }

    public function updateRecordRequestAsFailed(int $id) {

    }

    public function createRecord(object $message) {
        //$this->db->insert_record('ethos_message', $this->messageToRecord($message));
    }

    private function messageToRecord(object $message) : object {
        $record = new \stdClass();

        $record->id = $message->id;
        // TODO

        return $record;
    }
}