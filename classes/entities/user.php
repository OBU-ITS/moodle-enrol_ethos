<?php
namespace enrol_ethos\entities;

class user {
    public $id;
    public $username;
    public $userProfile;
    public $enrolments;

    public function __construct(
        $id, user_profile $userProfile)
    {
        $this->id = $id;
        $this->userProfile = $userProfile;

        $this->enrolments = array();
    }
}