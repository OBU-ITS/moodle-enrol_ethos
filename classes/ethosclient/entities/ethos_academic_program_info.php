<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_academic_program_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    // TODO

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        // TODO
    }
}