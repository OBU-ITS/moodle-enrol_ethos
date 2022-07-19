<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_subject_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    // Attributes
    public string $id; // required
    public string $abbreviation;
    public string $title;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->abbreviation = $data->abbreviation;
        $this->title = $data->title;

    }

}