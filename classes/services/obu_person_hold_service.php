<?php

use enrol_ethos\entities\mdl_user;
use enrol_ethos\ethosclient\entities\ethos_person_hold_info;

class obu_person_hold_service
{
    private function __construct()
    {
    }

    private static ?obu_person_hold_service $instance = null;

    public static function getInstance(): obu_person_hold_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function cleanHoldsProfileField(string $holds) : string {
        $obuPersonHolds = $this->deserializeHolds($holds);
        $updatedData = $this->cleanHolds($obuPersonHolds);
        return $this->serializeHolds($updatedData);
    }

    /**
     * @param obu_person_hold[] $holds
     * @return obu_person_hold[]
     */
    private function cleanHolds(array $holds) : array {
        // Todo
        // decode json to an array
        // move holds where the end date is in the past
        // encode to json
    }

    /**
     * @param string $holds
     * @return obu_person_hold[]
     */
    private function deserializeHolds(string $holds) : array {
        $data = json_decode($holds);

        if(!is_array($data)) {
            return array();
        }

        return array_map(function ($item) {
            return new obu_person_hold($item);
        }, $data);
    }

    /**
     * @param obu_person_hold[] $holds
     * @return string
     */
    private function serializeHolds(array $holds) : string
    {
        return json_encode($holds);
    }

    public function update(ethos_person_hold_info $ethosHold, mdl_user $user) {
        $personHoldsJson = $user->getCustomData()->personHolds;
        $personHoldsArray = $this->deserializeHolds($personHoldsJson);

        foreach ($personHoldsArray as $personHold){
            if (($personHold->id) === $ethosHold->id){
                $personHold->populateObjectByEthosPersonHold($ethosHold);
                break;
            }
        }

        $updatedHolds = $this->serializeHolds($personHoldsArray);
        $user->getCustomData()->personHolds = $updatedHolds;
    }

    public function remove(string $holdGuid, mdl_user $user) {
        $personHoldsJson = $user->getCustomData()->personHolds;
        $personHoldsArray = $this->deserializeHolds($personHoldsJson);
        $newPersonHoldsArray = array();

        foreach ($personHoldsArray as $personHold){
            if ($personHold->id !== $holdGuid){
                $newPersonHoldsArray[] = $personHold;
            }
        }

        $updatedHolds = $this->serializeHolds($newPersonHoldsArray);
        $user->getCustomData()->personHolds = $updatedHolds;
    }
}