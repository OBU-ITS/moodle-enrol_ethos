<?php
namespace enrol_ethos\entities;

class obu_user_enrolments_info
{
    public function __construct()
    {
        $this->userEnrolments = array();
    }

    /**
     * @var mdl_user_enrolment[]
     */
    private array $userEnrolments;

    /**
     * @return mdl_user_enrolment[]
     */
    public function getUserEnrolments() : array {
        return $this->userEnrolments;
    }

    public function addUserEnrolment(mdl_user_enrolment $userEnrolment) : array {
        $this->userEnrolments[] = $userEnrolment;
    }
}