<?php

namespace enrol_ethos\ethosclient\entities;

class name_info {

    public string $firstName;
    public string $lastName;
    public string $middleName;
    public string $fullName;
    public string $prefix;
    public string $suffix;
    public string $nameType;
    public string $nickName;

    public function toString() : string {
        return "NameInfo(firstName='$this->firstName', lastName='$this->lastName', middleName='$this->middleName', fullName='$this->fullName', prefix='$this->prefix', suffix='$this->suffix', nameType='$this->nameType', nickName='$this->nickName')";
    }

}