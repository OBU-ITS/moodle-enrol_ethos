<?php
//namespace enrol_ethos\repositories;
//use enrol_ethos\interfaces\ethos_message_repository_interface;
//use enrol_ethos\entities\ethos_message;
//
//class db_ethos_message_repository implements ethos_message_repository_interface
//{
//    protected $db;
//
//    public function __construct($db)
//    {
//        $this->db = $db;
//    }
//
//    public function getMaxId(): int {
//        $sql = "SELECT message_id FROM {enrol_ethos_message} ORDER BY message_id DESC LIMIT 1";
//
//        $result = $this->db->get_records_sql($sql);
//
//        return array_key_first($result) ?? 0;
//    }
//
//    public function getMessage($id): ?ethos_message
//    {
//        $item = $this->db->get_record('enrol_ethos_message', array('id' => $id));
//
//        return $this->mapToEthosMessage($item);
//    }
//
//    public function getMessageByMessageId($messageId): ?ethos_message
//    {
//        $item = $this->db->get_record('enrol_ethos_message', array('message_id' => $messageId));
//
//        return $this->mapToEthosMessage($item);
//    }
//
//    public function getMessages($limit = 0, $offset = 0, ?bool $processedState = null): array {
//        $sql = "SELECT * FROM {enrol_ethos_message}";
//
//        if(!is_null($processedState)) {
//            $sql .= $processedState
//                ? " WHERE processed = 1"
//                : " WHERE processed = 0";
//        }
//
//        if($limit > 0) {
//            $sql .= " LIMIT :limit";
//        }
//
//        if($offset > 0) {
//            $sql .= " OFFSET :offset";
//        }
//
//        $items = $this->db->get_records_sql($sql, ["offset"=>$offset, "limit"=>$limit]);
//
//        return $this->mapToEthosMessages($items);
//    }
//
//    private function mapToEthosMessage($item) : ?ethos_message {
//        if ($item) {
//            return new ethos_message($item->id,
//                $item->published,
//                $item->resource_name,
//                $item->resource_id,
//                $item->operation,
//                $item->person_id,
//                $item->processed);
//        }
//
//        return null;
//    }
//
//    private function mapToEthosMessages($items) : array {
//        $result = array();
//
//        if(!isset($items)) {
//            return $result;
//        }
//
//        foreach($items as $item){
//            $message = $this->mapToEthosMessage($item);
//
//            if(!is_null($message)) {
//                $result[] = $message;
//            }
//        }
//
//        return $result;
//    }
//
//    public function createMessage(ethos_message $message) : int {
//        $existingRecord = $this->getMessageByMessageId($message->message_id);
//
//        if(!isset($existingRecord)) {
//            return $this->db->insert_record_raw('enrol_ethos_message', $message);
//        }
//
//        return 0;
//    }
//
//    public function updateMessage(ethos_message $message) : bool {
//        return $this->db->update_record('enrol_ethos_message', $message);
//    }
//
//    public function removeMessage(int $id) {
//        $sql = "DELETE FROM {enrol_ethos_message} WHERE id = :id";
//
//        $this->db->get_record_sql($sql, ['id' => $id]);
//    }
//
//    public function removeMessagesOlderThan(int $date, ?bool $processedState = null) {
//        $sql = "DELETE FROM {enrol_ethos_message} WHERE published <= :published";
//
//        if(!is_null($processedState)) {
//            $sql .= $processedState
//                ? " AND processed = 1"
//                : " AND processed = 0";
//        }
//
//        $this->db->get_record_sql($sql, ['published' => $date]);
//    }
//}