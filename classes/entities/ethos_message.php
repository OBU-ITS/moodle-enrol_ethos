<?php
namespace enrol_ethos\entities;

class ethos_message {
    public ?int $id;
    public int $message_id;
    public int $published;
    public string $resource_name;
    public string $resource_id;
    public string $operation;
    public string $person_id;
    public bool $processed;

    public function __construct(?int $id, int $message_id, string $published, string $resource_name, string $resource_id, string $operation, string $person_id, int $processed=0) {
        $this->id = $id;
        $this->message_id = $message_id;
        $this->published = strtotime($published);
        $this->resource_name = $resource_name;
        $this->resource_id = $resource_id;
        $this->operation = $operation;
        $this->person_id = $person_id;
        $this->processed = $processed ? 1 : 0;
    }

    public function toString() : string {

        return "(ID: {$this->message_id}, Resource: {$this->resource_name}, Processed: {$this->processed})";
    }
}