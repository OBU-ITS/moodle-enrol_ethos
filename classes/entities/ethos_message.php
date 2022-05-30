<?php
namespace enrol_ethos\entities;

class ethos_message {
    public $id;
    public $published;
    public $resource_name;
    public $resource_id;
    public $operation;
    public $person_id;
    public $processed;

    public function __construct($id, $published, $resource_name, $resource_id, $operation, $person_id, $processed=0) {
        $this->id = $id;
        $this->published = $published;
        $this->resource_name = $resource_name;
        $this->resource_id = $resource_id;
        $this->operation = $operation;
        $this->person_id = $person_id;
        $this->processed = $processed;
    }
}