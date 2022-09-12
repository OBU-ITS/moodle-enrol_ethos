<?php

class obu_person_hold {
    private string $id;
    private int $startDateTime;
    private int $endDateTime;
    private string $typeCode;
    private string $typeTitle;
    private string $typeId;

    public function __construct(object $data) {
        $this->populateObject($data);
    }

    public function populateObject(object $data) {
        if (!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->startDateTime = strtotime($data->startOn);
        $this->endDateTime =strtotime($data->endOn);
        $this->typeCode = $data->typeCode;
        $this->typeTitle = $data->typeTitle;
        $this->typeId = $data->typeID;
    }
}
