<?php
namespace enrol_ethos\ethosclient\entities\consume;

class ethos_notifications {
    /**
     * @var ethos_notification[]
     */
    public array $persons;

    /**
     * @var ethos_notification[]
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

    public function addPerson(ethos_notification $messageModel) : bool {
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

    public function addStudentAcademicPrograms(ethos_notification $messageModel) : bool {
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