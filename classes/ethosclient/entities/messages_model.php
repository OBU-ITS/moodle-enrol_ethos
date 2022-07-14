<?php
namespace enrol_ethos\ethosclient\service;

class messages_model {
    /**
     * @var message_model[]
     */
    public array $persons;

    /**
     * @var message_model[]
     */
    public array $studentAcademicPrograms;

    public function __construct()
    {
        $this->persons = array();
        $this->studentAcademicPrograms = array();
    }

    public function hasPersons() : bool {
        return count($this->persons) > 0;
    }

    public function hasStudentAcademicPrograms() : bool {
        return count($this->studentAcademicPrograms) > 0;
    }

    public function isEmpty() : bool {
        return !($this->hasPersons() || $this->hasStudentAcademicPrograms());
    }

    public function addPerson(message_model $messageModel) : bool {
        if (!isset($messageModel->personId)) {
            return false;
        }

        if (!in_array($messageModel->personId, array_column($this->persons, 'personId')))
        {
            $this->persons[] = $messageModel;
            return true;
        }

        return false;
    }

    public function addStudentAcademicPrograms(message_model $messageModel) : bool {
        if (!isset($messageModel->resourceId)) {
            return false;
        }

        if (!in_array($messageModel->resourceId, array_column($this->studentAcademicPrograms, 'resourceId')))
        {
            $this->studentAcademicPrograms[] = $messageModel;
            return true;
        }

        return false;
    }
}