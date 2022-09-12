<?php

use enrol_ethos\entities\mdl_user;

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
        $data = json_decode($holds);

        if(!is_array($data)) {
            return $holds;
        }
        $updatedData = $this->cleanHolds($data);

        return json_encode($updatedData);
    }

    private function cleanHolds(array $holds) : array {
        // Todo
        // decode json to an array
        // move holds where the end date is in the past
        // encode to json
    }


}