<?php
namespace enrol_ethos\interfaces;
use enrol_ethos\entities\user;

interface user_repository_interface
{
    public function findOne($id);
    public function save(user $user);
    public function remove(user $user);
    public function getAllUsersWithProfileFieldData(string $profileFieldShortName);
    public function getUsersWithoutProfileFieldData(string $profileFieldShortName);
    public function getUsersByProfileField(string $profileFieldShortName, array $dataArray);
    public function getAllUsers();
}