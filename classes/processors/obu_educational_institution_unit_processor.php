<?php
namespace enrol_ethos\processors;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\processors\base\obu_processor;

class obu_educational_institution_unit_processor implements obu_processor {
    const RESOURCE_NAME = "educational-institution-units";

    function process(ethos_notification $message)
    {
        // TODO: Implement process() method.
    }
}