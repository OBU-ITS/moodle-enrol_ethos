<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_section_title_type_provider;

class ethos_section_title_info
{
    public function __construct($data)
    {
        $this->populateObject($data);
    }

    public string $value;

    private string $typeId;
    private ?ethos_section_title_type_info $type = null;
    public function getTypeId() : string {
        return $this->typeId;
    }
    public function setTypeId(string $id) {
        $this->typeId = $id;
        $this->type = null;
    }
    public function getType() : ethos_section_title_type_info
    {
        if(!$this->type) {
            $provider = ethos_section_title_type_provider::getInstance();
            $this->type = $provider->get($this->typeId);
        }

        return $this->type;
    }

    public function populateObject($data) {
        if (!isset($data)) {
            return;
        }

        $this->value = $data->value;

        if (isset($data->type)) {
            $this->setTypeId($data->type->id);
        }
    }
}