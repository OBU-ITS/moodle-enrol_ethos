<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_person_hold_provider;
use enrol_ethos\ethosclient\providers\ethos_student_advisor_relationship_provider;
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
     * @return ethos_student_advisor_relationship_info[]|null
     * @param $studentId
     */
    public function getAdvisors($studentId): ?array
    {
        if ($this->advisors == null){
            $provider = ethos_student_advisor_relationship_provider::getInstance();
            $this->advisors = $provider->getByAdvisorPersonGuid($studentId);
        }
        return $this->advisors;
    }

    /**
     * @var ethos_student_advisor_relationship_info[]|null
     */
    private ?array $advisorStudents = null;

    /**
     * @return ethos_student_advisor_relationship_info[]|null
     * @param $advisorId
     */
    public function getAdvisorStudents($advisorId): ?array
    {
        if ($this->advisorStudents == null){
            $provider = ethos_student_advisor_relationship_provider::getInstance();
            $this->advisorStudents = $provider->getByAdvisorPersonGuid($advisorId);
        }
        return $this->advisorStudents;
    }

    /**
     * @var ethos_person_hold_info[]|null
     */
    private ?array $personHolds = null;

    /**
     * @return ethos_person_hold_info[]|null
     * @param $personId
     */
    public function getPersonHolds($personId): ?array
    {
        if ($this->personHolds == null){
            $provider = ethos_person_hold_provider::getInstance();
            $this->personHolds = $provider->getByPersonGuid($personId);
        }
        return $this->personHolds;
    }

    private function populateObject($data){
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->setNames($data->names);
        $this->setCredentials($data->credentials);
        $this->setAlternativeCredentials($data->alternativeCredentials);
    }
}
