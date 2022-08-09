<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_person_provider;

class ethos_student_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;

    private string $personId;  // required
    private ?ethos_person_info $person = null;
    public function getPersonId() : string {
        return $this->personId;
    }
    public function setPersonId(string $id) {
        $this->personId = $id;
        $this->person = null;
    }
    public function getPerson() : ethos_person_info
    {
        if(!$this->person) {
            $provider = ethos_person_provider::getInstance();
            $this->person = $provider->get($this->personId);
        }

        return $this->person;
    }

    private function populateObject($data){
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;

        if(isset($data->person)) {
            $this->setPersonId($data->person->id);
        }
    }
}
