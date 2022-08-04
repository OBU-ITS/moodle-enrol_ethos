<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_person_hold_type_provider;
use enrol_ethos\ethosclient\providers\ethos_person_provider;

class ethos_person_hold_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $startOn;
    public string $endOn;

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

    private string $typeId;  // required
    private ?ethos_person_hold_type_info $type = null;
    public function getTypeId() : string {
        return $this->typeId;
    }
    public function setTypeId(string $id) {
        $this->typeId = $id;
        $this->type = null;
    }
    public function getType() : ethos_person_hold_type_info
    {
        if(!$this->type) {
            $provider = ethos_person_hold_type_provider::getInstance();
            $this->type = $provider->get($this->typeId);
        }

        return $this->type;
    }

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        if(isset($data->person)) {
            $this->setPersonId($data->person->id);
        }

        if(isset($data->type)) {
            $this->setTypeId($data->type->detail->id);
        }

        $this->id = $data->id;
        $this->startOn = $data->startOn;
        $this->endOn = $data->endOn;

    }
}