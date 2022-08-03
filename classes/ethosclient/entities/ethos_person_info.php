<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_person_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    // TODO


    private function populateObject($data){
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        // TODO
    }
}
