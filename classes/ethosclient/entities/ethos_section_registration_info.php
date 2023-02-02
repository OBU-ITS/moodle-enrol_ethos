<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_person_provider;
use enrol_ethos\ethosclient\providers\ethos_section_provider;


class ethos_section_registration_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $registrationStatus;
    public string $sectionRegistrationStatusReason;

    private string $registrantId;  // required
    private ?ethos_person_info $registrant = null;
    public function getRegistrantId() : string {
        return $this->registrantId;
    }
    public function setRegistrantId(string $id) {
        $this->registrantId = $id;
        $this->registrant = null;
    }
    public function getRegistrant() : ?ethos_person_info
    {
        if(!$this->registrant) {
            $provider = ethos_person_provider::getInstance();
            if($registrant = $provider->get($this->registrantId)) {
                $this->registrant = $registrant;
            }
        }

        return $this->registrant;
    }

    private string $sectionId;  // required
    private ?ethos_section_info $section = null;
    public function getSectionId() : string {
        return $this->sectionId;
    }
    public function setSectionId(string $id) {
        $this->sectionId = $id;
        $this->section = null;
    }
    public function getSection() : ethos_section_info
    {
        if(!$this->section) {
            $provider = ethos_section_provider::getInstance();
            if($section = $provider->get($this->sectionId)) {
                $this->section = $section;
            }
        }

        return $this->section;
    }

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->registrationStatus = $data->registrationStatus;
        $this->sectionRegistrationStatusReason = $data->sectionRegistrationStatusReason;

        if(isset($data->registrant)) {
            $this->setRegistrantId($data->registrant->id);
        }

        if(isset($data->section)) {
            $this->setSectionId($data->section->id);
        }
    }
}
