<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_person_info_credential
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $type;
    public string $value;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->type = $data->type;
        $this->value = $data->value;
    }
}
