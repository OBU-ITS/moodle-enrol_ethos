<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_academic_credential_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $abbreviation;
    public string $title;
    public string $type;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->abbreviation = $data->abbreviation;
        $this->title = $data->title;
        $this->type = $data->type;
    }
}