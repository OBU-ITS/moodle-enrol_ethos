<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_educational_institution_unit_provider;

class ethos_owning_institution_unit_info
{
    public function __construct($data)
    {
        $this->populateObject($data);
    }

    public int $ownershipPercentage;

    private string $institutionUnitId;
    public ?ethos_educational_institution_unit_info $institutionUnit = null;
    public function getInstitutionUnitId() : string {
        return $this->institutionUnitId;
    }
    public function setInstitutionUnitId(string $id) {
        $this->institutionUnitId = $id;
        $this->institutionUnit = null;
    }
    public function getInstitutionUnit() : ethos_educational_institution_unit_info
    {
        if(!$this->institutionUnit) {
            $provider = ethos_educational_institution_unit_provider::getInstance();
            $this->institutionUnit = $provider->get($this->institutionUnitId);
        }

        return $this->institutionUnit;
    }

    public function populateObject($data) {
        if (!isset($data)) {
            return;
        }

        $this->ownershipPercentage = $data->ownershipPercentage;

        if (isset($data->institutionUnit)) {
            $this->setInstitutionUnitId($data->institutionUnit->id);
        }
    }
}