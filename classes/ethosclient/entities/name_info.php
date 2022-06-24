<?php

namespace enrol_ethos\ethosclient\entities;

class name_info {

    public $firstName;
    public $lastName;
    public $middleName;
    public $fullName;
    public $prefix;
    public $suffix;
    public $nameType;
    public $nickName;

    public function toString() : string {
        return "NameInfo(firstName='$this->firstName', lastName='$this->lastName', middleName='$this->middleName', fullName='$this->fullName', prefix='$this->prefix', suffix='$this->suffix', nameType='$this->nameType', nickName='$this->nickName')";
    }

}