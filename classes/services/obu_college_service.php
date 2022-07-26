<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\entities\ethos_educational_institution_unit_info;
use enrol_ethos\ethosclient\entities\ethos_owning_institution_unit_info;

class obu_college_service
{
    private function __construct()
    {
    }

    private static ?obu_college_service $instance = null;
    public static function getInstance() : obu_college_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param ethos_owning_institution_unit_info[] $owningInstitutionUnits
     * @return ethos_educational_institution_unit_info|null
     */
    public function getCollege(array $owningInstitutionUnits) : ?ethos_educational_institution_unit_info {
        foreach($owningInstitutionUnits as $owningInstitutionUnit) {
            $institutionUnit = $owningInstitutionUnit->getInstitutionUnit();
            if($institutionUnit->type == "college") {
                return $institutionUnit;
            }
        }

        return null;
    }
}