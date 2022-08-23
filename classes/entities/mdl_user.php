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
    public string $userType;

    public function __construct()
    {
    }

    private ?mdl_user_profile $customData;
    public function getCustomData() : mdl_user_profile{
        if(!isset($this->customData)) {
            if($this->id > 0) {
                $service = mdl_user_service::getInstance();
                $this->customData = $service->getCustomData($this->id);
            }
            else {
                $this->customData = new mdl_user_profile();
            }
        }

        return $this->customData;
    }

    public function setCustomData($profile)
    {
        $this->customData = $profile;
    }
}