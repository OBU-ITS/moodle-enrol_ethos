<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_subject_provider;

class ethos_course_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    // Attributes
    public string $id; // required
    public string $number;

    // Single references
    private string $subjectId;  // required
    private ?ethos_subject_info $subject = null;
    public function getSubjectId() : string {
        return $this->subjectId;
    }
    public function setSubjectId(string $id) {
        $this->subjectId = $id;
        $this->subject = null;
    }
    public function getSubject() : ethos_subject_info
    {
        if(!$this->subject) {
            $provider = ethos_subject_provider::getInstance();
            $this->subject = $provider->get($this->subjectId);
        }

        return $this->subject;
    }

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->number = $data->number;

       if(isset($data->subject)) {
            $this->setSubjectId($data->subject->id);
        }

    }

}