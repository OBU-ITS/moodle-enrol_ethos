<?php
namespace enrol_ethos\entities;

class mdl_user_profile
{
    // All
    public string $personGuid;
    public string $pidm;

    // Student
    public string $financeHold;
    public string $academicHold;
    public string $serviceNeeds;
    public string $studentGuid;
    public string $studentAdviser;
    public string $studentCompletionDate;
    public string $studentAcademicPrograms;
    public string $studentStatus;

    // Staff
    public string $isAdviserFlag;
    public string $isModuleLeadFlag;

    public function __construct()
    {
    }
}