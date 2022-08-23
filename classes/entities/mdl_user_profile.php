<?php
namespace enrol_ethos\entities;

use profile_field_base;

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

    public function populateObject(array $customData) {
        $this->personGuid = $customData['person_guid'];
        $this->pidm = $customData['pidm'];
        $this->financeHold = $customData['finance_hold'];
        $this->academicHold = $customData['academic_hold'];
        $this->serviceNeeds = $customData['service_needs'];
        $this->studentGuid = $customData['student_guid'];
        $this->studentAdviser = $customData['student_adviser'];
        $this->studentCompletionDate = $customData['student_completion_date'];
        $this->studentAcademicPrograms = $customData['student_academic_programs'];
        $this->studentStatus = $customData['student_status'];
        $this->isAdviserFlag = $customData['is_adviser_flag'];
        $this->isModuleLeadFlag = $customData['is_module_lead_flag'];
    }


}