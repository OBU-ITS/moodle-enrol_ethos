<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_section_title_type_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $title;
    public string $code;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->code = $data->code;
        $this->title = $data->title;
     }

}