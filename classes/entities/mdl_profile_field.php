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
    public string $defaultdata;
    public string $param1;
    public string $param2;
    public string $param3;
    public string $param4;
    public string $param5;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }
        var_dump($data);
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
        $this->defaultdata = $data->defaultdata;
        $this->param1 = $data->param1;
        $this->param2 = $data->param2;
        $this->param3 = $data->param3;
        $this->param4 = $data->param4;
        $this->param5 = $data->param5;
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
        $this->defaultdata = $data["defaultdata"];
        $this->param1 = $data["param1"];
        $this->param2 = $data["param2"];
        $this->param3 = $data["param3"];
        $this->param4 = $data["param4"];
        $this->param5 = $data["param5"];
    }
}