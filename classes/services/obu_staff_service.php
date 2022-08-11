<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_user;
use enrol_ethos\entities\obu_users_info;
use enrol_ethos\ethosclient\entities\ethos_alternative_credential_type_info;
use enrol_ethos\ethosclient\entities\ethos_person_info;
use enrol_ethos\ethosclient\providers\ethos_person_provider;

class obu_staff_service
{
    private ethos_person_provider $personProvider;
    private obu_alternative_credential_service $alternativeCredentialService;

    private ethos_alternative_credential_type_info $employeeAlternativeCredentialType;

    private function __construct()
    {
        $this->personProvider = ethos_person_provider::getInstance();
        $this->alternativeCredentialService = obu_alternative_credential_service::getInstance();

        $this->employeeAlternativeCredentialType = $this->alternativeCredentialService->getEmployeeNumberAlternativeCredentialType();
    }

    private static ?obu_staff_service $instance = null;
    public static function getInstance(): obu_staff_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param obu_users_info $users
     * @param string $id
     */
    public function get(obu_users_info $users, string $id) {
        $program = $this->personProvider->get($id);

        $this->addPersonToUsers($users, $program);
    }

    /**
     * @param obu_users_info $users
     * @param $limit
     * @param $offset
     * @return int
     */
    public function getBatch(obu_users_info $users, $limit, $offset) : int {
        $persons = $this->personProvider->getBatch($limit, $offset);

        array_map(function($person) use ($users) {
            $this->addPersonToUsers($users, $person);
        }, $persons);

        return count($persons);
    }

    /**
     * @param obu_users_info $users
     */
    public function getAll(obu_users_info $users) {
        $persons = $this->personProvider->getAll();

        array_map(function($person) use ($users) {
            $this->addPersonToUsers($users, $person);
        }, $persons);
    }

    /**
     * @param obu_users_info $users
     * @param ethos_person_info $person
     */
    private function addPersonToUsers(obu_users_info $users, ethos_person_info $person)
    {
        if(!$this->alternativeCredentialService->hasAlternativeCredentialOfType($person, $this->employeeAlternativeCredentialType)) {
            return;
        }

        $user = new mdl_user();

        $users->addUser($user);
    }
}