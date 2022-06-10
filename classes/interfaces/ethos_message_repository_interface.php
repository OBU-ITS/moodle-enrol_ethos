<?php
namespace enrol_ethos\interfaces;
use enrol_ethos\entities\ethos_message;

interface ethos_message_repository_interface
{
    public function getMaxId(): int;
    public function getMessage($id) : ?ethos_message;
    public function getMessageByMessageId($messageId): ?ethos_message;
    public function getMessages($limit = 0, $offset = 0, ?bool $processedState = null): array;
    public function createMessage(ethos_message $message) : int;
    public function updateMessage(ethos_message $message) : bool;
    public function removeMessage(int $id);
    public function removeMessagesOlderThan(int $date, ?bool $processedState = null);
}