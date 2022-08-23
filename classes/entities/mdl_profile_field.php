<?php
namespace enrol_ethos\entities;

class mdl_profile_field {
    public int $id;
    public string $datatype;
    public string $shortname;
    public string $name;
    public string $description;
    public bool $required;
    public bool $locked;
    public bool $forceunique;
    public bool $signup;
    public bool $visible;
    public int $categoryid;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->shortname = $data->shortname;
        $this->name = $data->name;
        $this->description = $data->description;
        $this->categoryid = $data->categoryid;
        $this->datatype = $data->datatype;
        $this->required = $data->required;
        $this->locked = $data->locked;
        $this->forceunique = $data->forceunique;
        $this->signup = $data->signup;
        $this->visible = $data->visible;
        $this->id = $data->id;
    }

    public function populateObjectByArray(array $data) {
        if(!isset($data)) {
            return;
        }

        $this->shortname = $data["shortname"];
        $this->name = $data["name"];
        $this->description = $data["description"];
        $this->categoryid = $data["categoryid"];
        $this->datatype = $data["datatype"];
        $this->required = $data["required"];
        $this->locked = $data["locked"];
        $this->forceunique = $data["forceunique"];
        $this->signup = $data["signup"];
        $this->visible = $data["visible"];
        $this->id = $data["id"];
    }
}