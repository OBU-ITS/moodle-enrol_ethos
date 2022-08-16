<?php
namespace enrol_ethos\processors;

use enrol_ethos\ethosclient\entities\consume\ethos_notification;
use enrol_ethos\ethosclient\providers\ethos_student_academic_program_provider;
use enrol_ethos\processors\base\obu_processor;

class obu_student_academic_program_processor implements obu_processor {
    const RESOURCE_NAME = ethos_student_academic_program_provider::PATH;

    function process(ethos_notification $message)
    {
        // TODO: Implement process() method.
    }
}