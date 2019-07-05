<?php
namespace enrol_ethos;

class profile_category {
    public $name;
    public $sortorder;
    public $id;

    public function __construct($name, $sortorder, $id=0) {
        $this->name = $name;
        $this->sortorder = $sortorder;
        $this->id = $id;
    }
}