<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_advisor_type_provider;
use enrol_ethos\ethosclient\providers\ethos_person_provider;

class ethos_student_advisor_relationship_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $assignedPriority;
    public string $startOn;

    private string $advisorId;  // required
    private ?ethos_person_info $advisor = null;
    public function getAdvisorId() : string {
        return $this->advisorId;
    }
    public function setAdvisorId(string $id) {
        $this->advisorId = $id;
        $this->advisor = null;
    }
    public function getAdvisor() : ?ethos_person_info
    {
        if(!$this->advisor) {
            $provider = ethos_person_provider::getInstance();
            if($advisor = $provider->get($this->advisorId)) {
                $this->advisor = $advisor;
            }
        }

        return $this->advisor;
    }

    private string $advisorTypeId;  // required
    private ?ethos_advisor_type_info $advisorType = null;
    public function getAdvisorTypeId() : string {
        return $this->advisorTypeId;
    }
    public function setAdvisorTypeId(string $id) {
        $this->advisorTypeId = $id;
        $this->advisorType = null;
    }
    public function getAdvisorType() : ?ethos_advisor_type_info
    {
        if(!$this->advisorType) {
            $provider = ethos_advisor_type_provider::getInstance();
            if($advisorType = $provider->get($this->advisorTypeId))
            {
                $this->advisorType = $advisorType;
            }
        }

        return $this->advisorType;
    }

    private string $studentId;  // required
    private ?ethos_person_info $student = null;
    public function getStudentId() : string {
        return $this->studentId;
    }
    public function setStudentId(string $id) {
        $this->studentId = $id;
        $this->student = null;
    }
    public function getStudent() : ?ethos_person_info
    {
        if(!$this->student) {
            $provider = ethos_person_provider::getInstance();
            if($student = $provider->get($this->studentId)) {
                $this->student = $student;
            }
        }

        return $this->student;
    }

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->assignedPriority = $data->assignedPriority;
        $this->startOn = $data->startOn;

        if(isset($data->advisor)) {
            $this->setAdvisorId($data->advisor->id);
        }
        if(isset($data->advisorType)) {
            $this->setAdvisorTypeId($data->advisorType->id);
        }
        if(isset($data->student)) {
            $this->setStudentId($data->student->id);
        }
    }
}