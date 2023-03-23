<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_academic_discipline_provider;

class ethos_academic_program_info_discipline
{
    public function __construct($data)
    {
        $this->populateObject($data);
    }

    public string $startOn;
    public string $endOn;
    public string $programConstraint;

    private string $disciplineId;
    private ?ethos_academic_discipline_info $discipline = null;
    public function getDisciplineId() : string {
        return $this->disciplineId;
    }
    public function setDisciplineId(string $id) {
        $this->disciplineId = $id;
        $this->discipline = null;
    }
    public function getDiscipline() : ?ethos_academic_discipline_info
    {
        if(!$this->discipline) {
            $provider = ethos_academic_discipline_provider::getInstance();
            if($discipline = $provider->get($this->disciplineId)) {
                $this->discipline = $discipline;
            }
        }

        return $this->discipline;
    }

    public function populateObject($data) {
        if (!isset($data)) {
            return;
        }

        $this->startOn = $data->startOn;
        $this->endOn = $data->endOn;
        $this->programConstraint = $data->programConstraint;

        if (isset($data->discipline)) {
            $this->setDisciplineId($data->discipline->id);
        }
    }
}