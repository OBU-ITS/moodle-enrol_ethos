<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_alternative_credential_type_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $code;
    public string $title;


    private function populateObject($data){
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->code = $data->code;
        $this->title = $data->title;
    }
}
