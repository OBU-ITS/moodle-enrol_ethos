<?php
namespace enrol_ethos\entities;

use profile_field_base;

class mdl_user_profile
{
    // All
    public string $personGuid = '';
    public string $pidm = '';
    public string $userType = '';

    // Student
    public string $financeHold = '';
    public string $academicHold = '';
    public string $personHolds = '';
    public string $serviceNeeds = '';
    public string $studentGuid = '';
    public string $studentAdviser = '';
    public string $studentCompletionDate = '';
    public string $studentAcademicPrograms = '';
    public string $studentStatus = '';

    // Staff
    public string $isAdviserFlag = '';
    public string $isModuleLeadFlag = '';

    public function populateObject(array $customData) {
        $this->personGuid = $customData['person_guid'] ?? '';
        $this->pidm = $customData['pidm'] ?? '';
        $this->userType = $customData['user_type'] ?? '';
        $this->financeHold = $customData['finance_hold'] ?? '';
        $this->academicHold = $customData['academic_hold'] ?? '';
        $this->personHolds = $customData['person_holds'] ?? '';
        $this->serviceNeeds = $customData['service_needs'] ?? '';
        $this->studentGuid = $customData['student_guid'] ?? '';
        $this->studentAdviser = $customData['student_adviser'] ?? '';
        $this->studentCompletionDate = $customData['student_completion_date'] ?? '';
        $this->studentAcademicPrograms = $customData['student_academic_programs'] ?? '';
        $this->studentStatus = $customData['student_status'] ?? '';
        $this->isAdviserFlag = $customData['is_adviser_flag'] ?? '';
        $this->isModuleLeadFlag = $customData['is_module_lead_flag'] ?? '';
    }

    public function getStandardClass(int $userId) : \stdClass {
        $rtn = new \stdClass();

        $rtn->id = $userId;
        $rtn->profile_field_person_guid = $this->personGuid;
        $rtn->profile_field_pidm = $this->pidm;
        $rtn->profile_field_user_type = $this->userType;
        $rtn->profile_field_finance_hold = $this->financeHold;
        $rtn->profile_field_academic_hold = $this->academicHold;
        $rtn->profile_field_person_holds = $this->personHolds;
        $rtn->profile_field_service_needs = $this->serviceNeeds;
        $rtn->profile_field_student_guid = $this->studentGuid;
        $rtn->profile_field_student_adviser = $this->studentAdviser;
        $rtn->profile_field_student_completion_date = $this->studentCompletionDate;
        $rtn->profile_field_student_academic_programs = $this->studentAcademicPrograms;
        $rtn->profile_field_student_status = $this->studentStatus;
        $rtn->profile_field_is_adviser_flag = $this->isAdviserFlag;
        $rtn->profile_field_is_module_lead_flag = $this->isModuleLeadFlag;

        return $rtn;
    }
}