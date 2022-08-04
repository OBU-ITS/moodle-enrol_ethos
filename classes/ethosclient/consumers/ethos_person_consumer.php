<?php
namespace enrol_ethos\ethosclient\consumers;

use enrol_ethos\ethosclient\consumers\base\ethos_consumer;
use enrol_ethos\ethosclient\entities\consume\ethos_notifications;

class ethos_person_consumer implements ethos_consumer {
    private const RESOURCE_NAME = "persons";

    public function getResourceName(): string
    {
        return self::RESOURCE_NAME;
    }

    function addDataToMessages(ethos_notifications $messages, object $data)
    {
        // TODO: Implement addDataToMessages() method.
    }
}