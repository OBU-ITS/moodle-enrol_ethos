<?php
namespace enrol_ethos\ethosclient\entities\request;

class ethos_response {
    public $messages;
    public string $remainingCount;

    public function __construct($messages, $remainingCount = 0) {
        $this->messages = $messages ?? array();
        $this->remainingCount = $remainingCount;
    }
}