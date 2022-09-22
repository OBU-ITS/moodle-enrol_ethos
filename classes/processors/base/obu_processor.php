<?php
namespace enrol_ethos\processors\base;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;

interface obu_processor {
    function process(ethos_notification $message);
}