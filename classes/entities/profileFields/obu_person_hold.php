<?php
namespace enrol_ethos\entities\profileFields;

use enrol_ethos\ethosclient\entities\ethos_person_hold_info;

class obu_person_hold {
    public string $id;
    public int $startDateTime;
    public int $endDateTime;
    public string $typeCode;
    public string $typeTitle;
    public string $typeId;

    public function __construct() {
    }

    public function populateObjectByEthosPersonHold(ethos_person_hold_info $data) {

        $this->id = $data->id;
        $this->startDateTime = strtotime($data->startOn);
        $this->endDateTime =strtotime($data->endOn);
        $type = $data->getType();
        $this->typeCode = $type->code;
        $this->typeTitle = $type->title;
        $this->typeId = $type->id;
    }

    public function populateObject(object $data) {
        if (!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->startDateTime = strtotime($data->startOn);
        $this->endDateTime =strtotime($data->endOn);
        $this->typeCode = $data->typeCode;
        $this->typeTitle = $data->typeTitle;
        $this->typeId = $data->typeID;
    }


}
