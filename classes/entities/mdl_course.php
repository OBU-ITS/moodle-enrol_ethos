<?php
namespace enrol_ethos\entities;

use enrol_ethos\services\moodle\mdl_course_service;

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


    private ?mdl_course_custom_fields $customData;
    public function getCustomData() : mdl_course_custom_fields{
        if(!isset($this->customData)) {
            if($this->id > 0) {
                $service = mdl_course_service::getInstance();
                $this->customData = $service->getCustomData($this->id);
            }
            else {
                $this->customData = new mdl_course_custom_fields();
            }
        }

        return $this->customData;
    }

    public function setCustomData($courseProfile)
    {
        $this->customData = $courseProfile;
    }
}