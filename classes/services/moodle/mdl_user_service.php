<?php
namespace enrol_ethos\services\moodle;

use enrol_ethos\entities\mdl_user;
use enrol_ethos\entities\mdl_user_profile;
use enrol_ethos\entities\obu_users_info;
use enrol_ethos\repositories\db_user_repository;
use progress_trace;

class mdl_user_service
{
    const BANNER_GUID_FIELD = 'person_guid';

    private db_user_repository $userRepo;

    private function __construct()
    {
        global $DB;

        $this->userRepo = new db_user_repository($DB);
    }

    private static ?mdl_user_service $instance = null;
    public static function getInstance(): mdl_user_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getUserByPersonGuid($id) : ?mdl_user {
        $ids = array($id);

        $items = $this->userRepo->getUsersByProfileField($this::BANNER_GUID_FIELD, $ids);

        return count($items) > 0 ? $items[0] : null;
    }

    public function getCustomData(int $id) : mdl_user_profile {

        return new mdl_user_profile(); // TODO
    }

    public function handleUserCreation(progress_trace $trace, obu_users_info $users) {
        // TODO : Joe
    }
}