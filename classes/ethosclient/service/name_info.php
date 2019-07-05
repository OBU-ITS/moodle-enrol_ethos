<?php

namespace enrol_ethos\ethosclient\service;

class name_info {

    public $firstName;
    public $lastName;
    public $middleName;
    public $fullName;
    public $prefix;
    public $suffix;
    public $nameType;
    public $nickName;

    public function toString() {
        return "NameInfo(firstName='$firstName', lastName='$lastName', middleName='$middleName', fullName='$fullName', prefix='$prefix', suffix='$suffix', nameType='$nameType', nickName='$nickName')";
    }

}