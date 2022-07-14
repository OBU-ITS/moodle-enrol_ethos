<?php
namespace enrol_ethos\ethosclient\service;

class message_model {
    public string $messageId;
    public string$resourceId;
    public string $personId;

    public function __construct(string $messageId, string $resourceId, string $personId)
    {
        $this->messageId = $messageId;
        $this->resourceId = $resourceId;
        $this->personId = $personId;
    }
}