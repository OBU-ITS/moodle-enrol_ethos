<?php

namespace enrol_ethos\ethosclient\service;

class period_info {
    public $categoryType;
    public $categoryParentId;
    public $code;
    public $title;
    public $endOn;
    public $id;
    public $startOn;
    public $registration;

    public function toString() {
        return "PeriodInfo(categoryType=$categoryType, categoryParentId=$categoryParentId, code=$code, title=$title, endOn=$endOn, id=$id, startOn=$startOn, registration=$registration)";
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