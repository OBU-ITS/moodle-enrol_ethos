<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_user;
use enrol_ethos\entities\mdl_user_profile;
use enrol_ethos\entities\obu_users_info;
use enrol_ethos\ethosclient\entities\ethos_person_info;
use enrol_ethos\ethosclient\entities\ethos_person_info_credential;
use enrol_ethos\ethosclient\providers\ethos_person_provider;

class obu_student_service
{
    private ethos_person_provider $personProvider;
    private obu_person_name_service $personNameService;
    private obu_student_advisor_relationship_service $studentAdvisorRelationshipService;

    private function __construct()
    {
        $this->personProvider = ethos_person_provider::getInstance();
        $this->personNameService = obu_person_name_service::getInstance();
        $this->studentAdvisorRelationshipService = obu_student_advisor_relationship_service::getInstance();
    }

    private static ?obu_student_service $instance = null;
    public static function getInstance(): obu_student_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param obu_users_info $users
     * @param ethos_person_info $person
     */
    public function get(obu_users_info $users, ethos_person_info $person) {
        $this->addPersonToUsers($users, $person);
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
        $username = $this->getUserName($person->credentials);
        $preferredName = $this->personNameService->getPreferredName($person->names);

        $profile = new mdl_user_profile();
        $profile->personGuid = $person->id;
        $profile->pidm = $person->pidm;
        $profile->serviceNeeds = $person->serviceNeeds;
        $profile->studentGuid = $person->getStudent()->id ?? '';
        $profile->studentStatus = $person->getStudent()->status ?? '';
        $profile->userType = "student";

        $user = new mdl_user();
        $user->username = $username;
        $user->firstname = $preferredName->firstName;
        $user->lastname = $preferredName->lastName;
        $user->email = $username . '@brookes.ac.uk';
        $user->setCustomData($profile);

        $this->studentAdvisorRelationshipService->setStudentAdvisorRelationships($user, $person->getAdvisors());

        $users->addUser($user);
    }

    /**
     * @param ethos_person_info_credential[] $credentials
     * @return string
     */
    private function getUserName(array $credentials) : string {
        if(!$credentials) return '';

        $bannerId = '';
        foreach($credentials as $credential) {
            $type = $credential->type;
            if($type == 'bannerId') {
                $bannerId = $credential->value;
                break;
            }
        }

        return $bannerId;
    }
}