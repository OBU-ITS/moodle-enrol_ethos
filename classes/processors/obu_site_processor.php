<?php
namespace enrol_ethos\processors;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\processors\base\obu_processor;

class obu_site_processor implements obu_processor {
    const RESOURCE_NAME = "sites";

    function process(ethos_notification $message)
    {
        // TODO: Implement process() method.
    }
}