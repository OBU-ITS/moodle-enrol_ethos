<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_user;
use enrol_ethos\entities\profileFields\obu_person_hold;
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

    /**
     * @param string $holds
     * @return string
     */
    public function cleanHoldsProfileField(string $holds) : string {
        $obuPersonHolds = $this->deserializeHolds($holds);
        $updatedData = $this->cleanHolds($obuPersonHolds);
        return $this->serializeHolds($updatedData);
    }

    /**
     * @param obu_person_hold[] $personHoldsArray
     * @return obu_person_hold[]
     */
    private function cleanHolds(array $personHoldsArray) : array {
        $newPersonHoldsArray = array();

        foreach ($personHoldsArray as $personHold){
            if (strtotime($personHold->endOn) > strtotime(date('Y-m-d', time()))){
                $newPersonHoldsArray[] = $personHold;
            }
        }

        return $newPersonHoldsArray;
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
            $hold = new obu_person_hold();
            $hold->populateObject($item);
            return $hold;
        }, $data);
    }

    /**
     * @param obu_person_hold[] $holds
     * @return string
     */
    private function serializeHolds(array $holds) : string
    {
        return count($holds) == 0
            ? ""
            : json_encode($holds);
    }

    /**
     * @param ethos_person_hold_info $ethosHold
     * @param mdl_user $user
     * @param bool $cleanHolds Default true
     * @return bool
     */
    public function update(ethos_person_hold_info $ethosHold, mdl_user $user, bool $cleanHolds = true) : bool {
        $personHoldsJson = $user->getCustomData()->personHolds;
        $personHoldsArray = $this->deserializeHolds($personHoldsJson);

        $updated = false;
        foreach ($personHoldsArray as $personHold){
            if ($personHold->id === $ethosHold->id){
                $personHold->populateObjectByEthosInfo($ethosHold);
                $updated = true;
                break;
            }
        }

        if($cleanHolds) {
            $personHoldsArray = $this->cleanHolds($personHoldsArray);
        }

        if (!$updated){
            if (strtotime($ethosHold->endOn) <= strtotime(date('Y-m-d', time()))){
                return false;
            }

            $hold = new obu_person_hold();
            $hold->populateObjectByEthosInfo($ethosHold);
            $personHoldsArray[] = $hold;
        }

        $updatedHolds = $this->serializeHolds($personHoldsArray);
        $user->getCustomData()->personHolds = $updatedHolds;

        return true;
    }

    /**
     * @param string $holdGuid
     * @param mdl_user $user
     */
    public function remove(string $holdGuid, mdl_user $user) {
        $personHoldsJson = $user->getCustomData()->personHolds;
        $personHoldsArray = $this->deserializeHolds($personHoldsJson);
        $newPersonHoldsArray = array();

        foreach ($personHoldsArray as $personHold){
            if ($personHold->id !== $holdGuid){
                $newPersonHoldsArray[] = $personHold;
            }
        }

        $newPersonHoldsArray = $this->cleanHolds($newPersonHoldsArray);
        $updatedHolds = $this->serializeHolds($newPersonHoldsArray);
        $user->getCustomData()->personHolds = $updatedHolds;
    }
}