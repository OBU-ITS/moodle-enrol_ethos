<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_educational_institution_unit_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $code
    public string $title;
    public string $type;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->code = $data->code;
        $this->type = $data->type;
        $this->title = $data->title;
    }

}