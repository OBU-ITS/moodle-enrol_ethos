<?php

namespace enrol_ethos\ethosclient\entities;

class ethos_course_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    // Attributes
    public string $id; // required

}