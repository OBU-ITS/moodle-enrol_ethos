<?php
//namespace enrol_ethos\services;
//
//use enrol_ethos\interfaces\ethos_message_repository_interface;
//use enrol_ethos\entities\ethos_message;
//
//class ethos_message_service
//{
//    private ethos_message_repository_interface $messageRepository;
//
//    public function __construct(ethos_message_repository_interface $messageRepository)
//    {
//        $this->messageRepository = $messageRepository;
//    }
//
//    public function getLastProcessedId() : int {
//        return $this->messageRepository->getMaxId();
//    }
//
//    public function getAllMessages() : array {
//        return $this->messageRepository->getMessages();
//    }
//
//    public function create(ethos_message $message) {
//        $this->messageRepository->createMessage($message);
//    }
//
//    public function update(ethos_message $message) {
//        $this->messageRepository->updateMessage($message);
//    }
//
//    public function save(ethos_message $message) {
//        if(!isset($message)) {
//            return;
//        }
//
//        $existingMessage = $this->messageRepository->getMessage($message->id);
//
//        isset($existingMessage) ? $this->updateMessage($message) : $this->createMessage($message);
//    }
//
//    public function purgeOlderThan($date) {
//        $this->messageRepository->removeMessagesOlderThan($date);
//    }
//}