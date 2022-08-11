<?php
namespace enrol_ethos\entities;

use enrol_ethos\services\moodle\mdl_user_service;

class mdl_user
{
    public int $id = 0;
    public string $username;
    public string $firstname;
    public string $lastname;
    public string $email;

    public function __construct()
    {

    }

    private ?mdl_user_profile $profile;
    public function getCustomData() : mdl_user_profile{
        if(!isset($this->profile)) {
            if($this->id > 0) {
                $service = mdl_user_service::getInstance();
                $this->profile = $service->getCustomData($this->id);
            }
            else {
                $this->profile = new mdl_user_profile();
            }
        }

        return $this->profile;
    }
}