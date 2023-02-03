<?php
namespace enrol_ethos\entities\profileFields;

use enrol_ethos\ethosclient\entities\ethos_person_hold_info;

class obu_person_hold {
    public string $id;
    public string $startOn;
    public string $endOn;
    public string $typeCode;
    public string $typeTitle;
    public string $typeID;

    public function __construct() {
    }

    public function populateObjectByEthosInfo(ethos_person_hold_info $data) {

        $this->id = $data->id;
        $this->startOn = $data->startOn;
        $this->endOn = $data->endOn;
        $type = $data->getType();
        $this->typeCode = $type->code;
        $this->typeTitle = $type->title;
        $this->typeID = $type->id;
    }

    public function populateObject(object $data) {
        if (!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->startOn = $data->startOn;
        $this->endOn = $data->endOn;
        $this->typeCode = $data->typeCode;
        $this->typeTitle = $data->typeTitle;
        $this->typeID = $data->typeID;
    }
}
