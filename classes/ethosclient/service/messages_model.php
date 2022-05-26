<?php
namespace enrol_ethos\ethosclient\service;

class messages_model {
    public $persons;
    public $studentAcademicPrograms;

    public function __construct()
    {
        $this->persons = array();
        $this->studentAcademicPrograms = array();
    }

    public function addPerson($messageModel)
    {
        if (!isset($messageModel->personId)) {
            return;
        }

        if (!in_array($messageModel->personId, array_column($this->persons, 'personId')))
        {
            $this->persons[] = $messageModel;
        }
    }

    public function addStudentAcademicPrograms($messageModel)
    {
        if (!isset($messageModel->resourceId)) {
            return;
        }

        if (!in_array($messageModel->resourceId, array_column($this->studentAcademicPrograms, 'resourceId')))
        {
            $this->studentAcademicPrograms[] = $messageModel;
        }
    }
}