<?php
namespace enrol_ethos\entities;

class profile_field {
    public $datatype;
    public $shortname;
    public $name;
    public $description;
    public $required;
    public $locked;
    public $forceunique;
    public $signup;
    public $visible;
    public $categoryid;

    public function __construct($shortname, $name, $description, $categoryid, $datatype='text', $required=0, $locked=0, $forceunique=0, $signup=0, $visible=1) {
        $this->datatype = $datatype;
        $this->shortname = $shortname;
        $this->name = $name;
        $this->description = $description; 
        $this->required = $required;
        $this->locked = $locked;
        $this->forceunique = $forceunique;
        $this->signup = $signup;
        $this->visible = $visible;
        $this->categoryid = $categoryid;
    }
}