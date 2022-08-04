<?php
namespace enrol_ethos\ethosclient\consumers\base;

use enrol_ethos\ethosclient\entities\consume\ethos_notifications;

interface ethos_consumer {
    function getResourceName() : string;
    function addDataToMessages(ethos_notifications $messages, object $data);
}