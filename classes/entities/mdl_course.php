<?php
namespace enrol_ethos\entities;

class mdl_course {
    public $idnumber;
    public ?int $id;
    public $name;
    public $shortname;
    public $catid;
    public $meta;
    public $startdate;
    public $enddate;
    public $visible;
    public $bannerId = "";

    public function __construct(
        $idnumber, $shortname, $name, $catid = '',
            $id = null, $startdate = 0, $enddate = 0, $meta=false, $visible=false)
    {
        $this->idnumber = $idnumber;
        $this->id = $id;
        $this->name = $name;
        $this->shortname = $shortname;
        $this->catid = $catid;
        $this->meta = $meta;
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->visible = $visible;
    }
}