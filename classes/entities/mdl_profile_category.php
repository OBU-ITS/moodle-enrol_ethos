<?php
namespace enrol_ethos\entities;

class mdl_profile_category {
    public string $name;
    public int $sortOrder;
    public int $id;

    public function __construct(?object $data = null) {
        $this->populateObject($data);
    }

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->name = $data->name;
        $this->sortOrder = $data->sortorder;
        $this->id = $data->id;
    }
}