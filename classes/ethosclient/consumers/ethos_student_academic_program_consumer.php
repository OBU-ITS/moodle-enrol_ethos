<?php
namespace enrol_ethos\ethosclient\consumers;

use enrol_ethos\ethosclient\consumers\base\ethos_consumer;
use enrol_ethos\ethosclient\entities\consume\ethos_notifications;

class ethos_student_academic_program_consumer implements ethos_consumer {
    private const RESOURCE_NAME = "student-academic-programs";

    public function getResourceName(): string
    {
        return self::RESOURCE_NAME;
    }

    public function addDataToMessages(object $data, ethos_notifications $messages): string
    {
        // TODO: Implement addDataToMessages() method.
        echo "Add student-academic-program data";
    }
}