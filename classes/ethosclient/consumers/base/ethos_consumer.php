<?php
namespace enrol_ethos\ethosclient\consumers\base;

use enrol_ethos\ethosclient\entities\consume\ethos_notifications;

interface ethos_consumer {
    function getResourceName() : string;
    function addDataToMessages(object $data, ethos_notifications $messages) : string;
}