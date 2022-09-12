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
        // TODO :
        // convert string to datetime where appropriate
    }
}
