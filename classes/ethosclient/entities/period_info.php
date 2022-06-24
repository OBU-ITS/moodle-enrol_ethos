<?php

namespace enrol_ethos\ethosclient\entities;

class period_info {
    public $categoryType;
    public $categoryParentId;
    public $code;
    public $title;
    public $endOn;
    public $id;
    public $startOn;
    public $registration;

    public function toString() : string {
        return "PeriodInfo(categoryType=$this->categoryType, categoryParentId=$this->categoryParentId, code=$this->code, title=$this->title, endOn=$this->endOn, id=$this->id, startOn=$this->startOn, registration=$this->registration)";
    }

    public function __construct($categoryType, $categoryParentId, $code, $title, \DateTime $endOn, $id, \DateTime $startOn, $registration) {
        $this->categoryType = $categoryType;
        $this->categoryParentId = $categoryParentId;
        $this->code = $code;
        $this->title = $title;
        $this->endOn = $endOn;
        $this->id = $id;
        $this->startOn = $startOn;
        $this->registration = $registration;
    }
}