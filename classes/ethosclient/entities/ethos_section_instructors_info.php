<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_person_provider;
use enrol_ethos\ethosclient\providers\ethos_section_provider;

class ethos_section_instructors_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $workStartOn;
    public string $workEndOn;


    private string $instructorId;
    private ?ethos_person_info $instructor = null;
    public function getInstructorId() : string {
        return $this->instructorId;
    }
    public function setInstructorId(string $id) {
        $this->instructorId = $id;
        $this->instructor = null;
    }
    public function getInstructor() : ?ethos_person_info
    {
        if(!$this->instructor) {
            $provider = ethos_person_provider::getInstance();
            if($instructor = $provider->get($this->instructorId)) {
                $this->instructor = $instructor;
            }
        }

        return $this->instructor;
    }

    private string $sectionId;
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
        $this->workStartOn = $data->workStartOn;
        $this->workEndOn = $data->workEndOn;

        if(isset($data->instructor)) {
            $this->setInstructorId($data->instructor->id);
        }
        if(isset($data->section)) {
            $this->setSectionId($data->section->id);
        }
    }
}