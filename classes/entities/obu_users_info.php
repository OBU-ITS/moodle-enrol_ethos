<?php
namespace enrol_ethos\entities;

class obu_users_info
{
    public function __construct()
    {
        $this->users = array();
    }

    /**
     * @var mdl_user[]
     */
    private array $users;

    /**
     * @return mdl_user[]
     */
    public function getUsers() : array {
        return $this->users;
    }

    public function addUser(mdl_user $user) : array {
        $this->users[] = $user;
    }
}