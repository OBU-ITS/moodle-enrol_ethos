<?php
namespace enrol_ethos\processors;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\ethosclient\providers\ethos_person_hold_provider;
use enrol_ethos\processors\base\obu_processor;

class obu_person_hold_processor implements obu_processor {
    const RESOURCE_NAME = ethos_person_hold_provider::PATH;

    function process(ethos_notification $message)
    {
        // TODO: Implement process() method.
    }
}