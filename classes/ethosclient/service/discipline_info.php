<?php

namespace enrol_ethos\ethosclient\service;

class discipline_info
{
    /** Field of study */
    public $disciplineCode;
    public $disciplineType;
    public $disciplineTitle;

    public function toString()
    {
        return "DisciplineInfo(disciplineCode=$disciplineCode, disciplineType=$disciplineType, disciplineTitle=$disciplineTitle)";
    }

    public function __construct($disciplineCode, $disciplineType, $disciplineTitle) {
        $this->disciplineCode = $disciplineCode;
        $this->disciplineType = $disciplineType;
        $this->disciplineTitle = $disciplineTitle;
    }
}