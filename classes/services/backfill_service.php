<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\user;
use enrol_ethos\ethosclient\client\ethos_client;
use enrol_ethos\ethosclient\service\ethos_person_provider;
use enrol_ethos\repositories\db_user_repository;

class backfill_service
{
    const EMPTY_GUID = '00000000-0000-0000-0000-000000000000';

    private db_user_repository $userRepository;
    private progress_trace $trace;
    private ethos_person_provider $personService;

    public function __construct($trace)
    {
        global $DB;

        $this->userRepository = new db_user_repository($DB);

        $this->personService = ethos_person_provider::getInstance();

        $this->trace = $trace;
    }

    public function backfillUserBannerGuids() {
        $users = $this->userRepository->getUsersWithoutProfileFieldData(user_service::BANNER_GUID, 'ldap');
        if(count($users) == 0) {
            $this->trace->output("All LDAP users have populated Banner Guids.");
            return;
        }
        else {
            $this->trace->output(count($users) . "LDAP users require Banner Guids.");
        }

        foreach($users as $user) {
            $person = $this->getBannerPersonRecord($user);
            $bannerGuid = isset($person)
                ? $person->id
                : backfill_service::EMPTY_GUID;

            //$user->pro
        }
    }

    /**
     * @param user $user
     * @return object|null
     */
    private function getBannerPersonRecord(user $user) : ?object {
//        $person = $this->personService->getPersonsByBannerId($user->username);
//        if (isset($person)) {
//            return $person;
//        }
//
//        $person = $this->personService->getPersonByEmployeeAlternativeCredential($user->username);
//        if (isset($person)) {
//            return $person;
//        }
//
//        return null;
    }

    /**
     * @param user[] $users
     * @return user[]
     */
    private function updateUsersByPidm(array $users) : array {

    }
}