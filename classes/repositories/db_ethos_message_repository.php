<?php
namespace enrol_ethos\repositories;
use enrol_ethos\interfaces\ethos_message_interface;
use enrol_ethos\entities\ethos_message;

class db_ethos_message_repository implements ethos_message_repository_interface
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getMessage($id): ?ethos_message
    {
        $field = $this->db->get_record('enrol_ethos_message', array('id' => $id));

        if ($field) {
            return new ethos_message($field->id,
                $field->published,
                $field->resource_name,
                $field->resource_id,
                $field->operation,
                $field->person_id,
                $field->processed);
        }

        return null;
    }

    public function getMessages(): array {
        return array();
    }

    public function getUnprocessedMessages() : array {
        return array();
    }

    public function getMaxId(): int {
        return 0;
    }

    public function save(ethos_message $profileField) {

    }

    public function remove(ethos_message $profileField) {

    }
}