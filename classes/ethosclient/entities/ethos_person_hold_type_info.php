<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_person_hold_type_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $title;
    public string $code;
    public string $category;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->code = $data->code;
        $this->title = $data->title;
        $this->category = $data->category;
    }
}