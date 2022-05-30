<?php
namespace enrol_ethos\interfaces;
use enrol_ethos\entities\ethos_message;

interface ethos_message_repository_interface
{
    public function getMessage($id) : ?ethos_message;
    public function getUnprocessedMessages() : array;
    public function getMessages(): array;
    public function getMaxId(): int;
    public function save(ethos_message $profileField);
    public function remove(ethos_message $profileField);
}