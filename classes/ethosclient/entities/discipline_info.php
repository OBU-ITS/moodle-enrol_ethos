<?php

namespace enrol_ethos\ethosclient\entities;

class discipline_info
{
    /** Field of study */
    public string $disciplineCode;
    public string $disciplineType;
    public string $disciplineTitle;

    public function toString() : string
    {
        return "DisciplineInfo(disciplineCode=$this->disciplineCode, disciplineType=$this->disciplineType, disciplineTitle=$this->disciplineTitle)";
    }

    public function __construct($disciplineCode, $disciplineType, $disciplineTitle) {
        $this->disciplineCode = $disciplineCode;
        $this->disciplineType = $disciplineType;
        $this->disciplineTitle = $disciplineTitle;
    }
}