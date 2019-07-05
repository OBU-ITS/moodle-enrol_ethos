<?php

namespace enrol_ethos\ethosclient;

class AcademicProgram {

    public $academicLevel;
    public $authorizing;
    public $code;
    public $credentials;
    public $disciplines;
    public $id;
    public $startOn;
    public $status;
    public $title;
    
    public function toString() {
        return "AcademicProgram(academicLevel=$academicLevel, authorizing=$authorizing, code=$code, credentials=$credentials, disciplines=$disciplines, id=$id, startOn=$startOn, status=$status, title=$title)";
    }

}