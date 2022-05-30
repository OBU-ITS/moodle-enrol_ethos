<?php
namespace enrol_ethos\services;

use enrol_ethos\interfaces\ethos_message_repository_interface;
use enrol_ethos\entities\ethos_message;

class ethos_message_service
{
    private $messageRepository;

    public function __construct(ethos_message_repository_interface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function getLastProcessedId() : int {
        return $this->messageRepository->getMaxId();
    }


}