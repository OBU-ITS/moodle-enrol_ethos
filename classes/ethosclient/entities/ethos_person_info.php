<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_person_hold_provider;
use enrol_ethos\ethosclient\providers\ethos_section_instructors_provider;
use enrol_ethos\ethosclient\providers\ethos_student_advisor_relationship_provider;
use enrol_ethos\ethosclient\providers\ethos_student_provider;
use enrol_ethos\ethosclient\services\ethos_person_alternative_credential_service;
use enrol_ethos\ethosclient\services\ethos_person_credential_service;
use enrol_ethos\ethosclient\services\ethos_person_name_service;

class ethos_person_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $pidm;
    public string $serviceNeeds;

    /**
     * @var ethos_person_info_name[]
     */
    public array $names;

    /**
     * @param object[] $nameObjs
     */
    private function setNames(array $nameObjs)
    {
        $service = ethos_person_name_service::getInstance();

        $this->names = array();
        foreach($nameObjs as $nameObj) {
            $this->names[] = $service->get($nameObj);
        }
    }

    /**
     * @var ethos_person_info_credential[]
     */
    public array $credentials;

    /**
     * @param object[] $credentialObjs
     */
    private function setCredentials(array $credentialObjs)
    {
        $service = ethos_person_credential_service::getInstance();

        $this->credentials = array();
        foreach($credentialObjs as $credentialObj) {
            $this->credentials[] = $service->get($credentialObj);
        }
    }

    /**
     * @var ethos_person_info_alternative_credential[]
     */
    public array $alternativeCredentials;

    /**
     * @param object[] $alternativeCredentialObjs
     */
    private function setAlternativeCredentials(array $alternativeCredentialObjs)
    {
        $service = ethos_person_alternative_credential_service::getInstance();

        $this->alternativeCredentials = array();
        foreach($alternativeCredentialObjs as $alternativeCredentialObj) {
            $this->alternativeCredentials[] = $service->get($alternativeCredentialObj);
        }
    }

    /**
     * @var ethos_student_advisor_relationship_info[]|null
     */
    private ?array $advisors = null;

    /**
     * @return ethos_student_advisor_relationship_info[]
     */
    public function getAdvisors(): array
    {
        if ($this->advisors == null){
            $provider = ethos_student_advisor_relationship_provider::getInstance();
            $this->advisors = $provider->getByStudentPersonGuid($this->id);
        }

        return $this->advisors;
    }

    /**
     * @var ethos_student_advisor_relationship_info[]|null
     */
    private ?array $advisorStudents = null;

    /**
     * @return ethos_student_advisor_relationship_info[]
     */
    public function getAdvisorStudents(): array
    {
        if ($this->advisorStudents == null){
            $provider = ethos_student_advisor_relationship_provider::getInstance();
            $this->advisorStudents = $provider->getByAdvisorPersonGuid($this->id);
        }
        return $this->advisorStudents;
    }

    /**
     * @var ethos_person_hold_info[]|null
     */
    private ?array $personHolds = null;

    /**
     * @return ethos_person_hold_info[]
     */
    public function getPersonHolds(): array
    {
        if ($this->personHolds == null){
            $provider = ethos_person_hold_provider::getInstance();
            $this->personHolds = $provider->getByPersonGuid($this->id);
        }
        return $this->personHolds;
    }

    /**
     * @var ethos_section_instructors_info[]|null
     */
    private ?array $instructorSections = null;

    /**
     * @return ethos_section_instructors_info[]
     */
    public function getInstructorSections(): array
    {
        if ($this->instructorSections == null){
            $provider = ethos_section_instructors_provider::getInstance();
            $this->instructorSections = $provider->getByInstructorPersonGuid($this->id);
        }
        return $this->instructorSections;
    }

    private ?ethos_student_info $student = null;

    public function getStudent(): ethos_student_info
    {
        if ($this->student == null){
            $provider = ethos_student_provider::getInstance();
            $this->student = $provider->getStudentByPersonId($this->id);
        }
        return $this->student;
    }

    private function populateObject($data){
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->pidm = $data->pidm ?? "";
        $this->serviceNeeds = json_encode($data->obu_StudentSupportNeeds ?? "");
        $this->setNames($data->names);
        $this->setCredentials($data->credentials);
        if (isset($data->alternativeCredentials)){
            $this->setAlternativeCredentials($data->alternativeCredentials);

        }
    }
}
