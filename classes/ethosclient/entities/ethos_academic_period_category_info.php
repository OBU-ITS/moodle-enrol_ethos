<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_academic_period_provider;

class ethos_academic_period_category_info
{
    public function __construct($data)
    {
        $this->populateObject($data);
    }

    public string $type;

    private string $parentId;
    private ?ethos_academic_period_info $parent = null;
    public function getParentId() : string {
        return $this->parentId;
    }
    public function setParentId(string $id) {
        $this->parentId = $id;
        $this->parent = null;
    }
    public function getParent() : ?ethos_academic_period_info
    {
        if(!isset($this->parentId)){
            return null;
        }

        if(!$this->parent) {
            $provider = ethos_academic_period_provider::getInstance();
            $this->parent = $provider->get($this->parentId);
        }

        return $this->parent;
    }

    public function populateObject(object $data)
    {
        if (!isset($data)) {
            return;
        }

        $this->type = $data->type;

        if (isset($data->parent)) {
            if (isset($data->parent->academicPeriod)) {
                $this->setParentId($data->parent->academicPeriod->id);
            }
            else {
                $this->setParentId($data->parent->id);
            }
        }
    }



}