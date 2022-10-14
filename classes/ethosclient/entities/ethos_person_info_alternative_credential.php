<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_alternative_credential_type_provider;

class ethos_person_info_alternative_credential
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $value;


    private string $typeId;
    private ?ethos_alternative_credential_type_info $type = null;

    public function getTypeId() : string {
        return $this->typeId;
    }
    public function setTypeId(string $id) {
        $this->typeId = $id;
        $this->type = null;
    }
    public function getType() : ethos_alternative_credential_type_info
    {
        if(!$this->type) {
            $provider = ethos_alternative_credential_type_provider::getInstance();
            $this->type = $provider->get($this->typeId);
        }

        return $this->type;
    }


    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }
        if(isset($data->type)) {
            $this->setTypeId($data->type->id);
        }

        $this->value = $data->value;

    }
}