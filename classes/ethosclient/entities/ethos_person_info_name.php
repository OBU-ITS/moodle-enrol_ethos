<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_person_info_name
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $firstName;
    public string $lastName;
    public string $fullName;
    public string $preference;
    public string $category;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }
        if(isset($data->type)) {
            $this->category = $data->type->category;
        }

        $this->id = $data->id;
        $this->firstName = $data->firstName;
        $this->lastName = $data->lastName;
        $this->fullName = $data->fullName;
        $this->preference = $data->preference;
    }
}