<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_academic_program_provider;
use enrol_ethos\ethosclient\providers\ethos_student_provider;

class ethos_student_academic_program_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $obu_SorlcurCactCode;
    public string $obu_SorlcurLmodCode;

    private string $studentId;  // required
    private ?ethos_student_info $student = null;
    public function getStudentId() : string {
        return $this->studentId;
    }
    public function setStudentId(string $id) {
        $this->studentId = $id;
        $this->student = null;
    }
    public function getStudent() : ?ethos_student_info
    {
        if(!$this->student) {
            $provider = ethos_student_provider::getInstance();
            if($student = $provider->get($this->studentId)) {
                $this->student = $student;
            }
        }

        return $this->student;
    }

    private string $programId;  // required
    private ?ethos_academic_program_info $program = null;
    public function getProgramId() : string {
        return $this->programId;
    }
    public function setProgramId(string $id) {
        $this->programId = $id;
        $this->program = null;
    }
    public function getProgram() : ?ethos_academic_program_info
    {
        if(!$this->program) {
            $provider = ethos_academic_program_provider::getInstance();
            $this->program = $provider->get($this->programId);
            if($program = $provider->get($this->programId)) {
                $this->program = $program;
            }
        }

        return $this->program;
    }

    private function populateObject($data){
        if(!isset($data)) {
            return;
        }

        if(isset($data->student)) {
            $this->setStudentId($data->student->id);
        }

        if(isset($data->program)) {
            $this->setProgramId($data->program->id);
        }

        $this->id = $data->id;
        $this->obu_SorlcurCactCode = $data->obu_SorlcurCactCode;
        $this->obu_SorlcurLmodCode = $data->obu_SorlcurLmodCode;
    }
}
