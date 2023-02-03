<?php
namespace enrol_ethos\entities\profileFields;

use enrol_ethos\ethosclient\entities\ethos_student_advisor_relationship_info;

class obu_student_advisor_relationship {
    public string $id;
    public string $startOn;
    public string $advisorGuid;
    public string $advisorType;
    public string $assignedPriority;

    public function __construct() {
    }

    public function populateObjectByEthosInfo(ethos_student_advisor_relationship_info $data) {

        $this->id = $data->id;
        $this->startOn = $data->startOn;
        $this->advisorGuid = $data->getAdvisorId();
        $type = $data->getAdvisorType();
        $this->advisorType = $type->title;
        $this->assignedPriority = ucfirst($data->assignedPriority);
    }

    public function populateObject(object $data) {
        if (!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->startOn = $data->startOn;
        $this->advisorGuid = $data->advisorGuid;
        $this->advisorType = $data->advisorType;
        $this->assignedPriority = $data->assignedPriority;
    }
}
