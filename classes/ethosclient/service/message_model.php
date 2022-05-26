<?php
namespace enrol_ethos\ethosclient\service;

class message_model {
    public $messageId;
    public $resourceId;
    public $personId;

    public function __construct($messageId, $resourceId, $personId)
    {
        $this->messageId = $messageId;
        $this->resourceId = $resourceId;
        $this->personId = $personId;
    }
}