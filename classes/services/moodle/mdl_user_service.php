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

    /**
     * Get LDAP Users
     *
     * @param int $limit
     * @param int $offset
     * @return mdl_user[] users with the LDAP auth type
     */
    public function getLdapUsers(int $limit, int $offset) : array {
        $this->userRepo->getUsersByAuthType("ldap", $limit, $offset);
    }

    public function handleUserCreation(progress_trace $trace, obu_users_info $users) {
        $users = $users->getUsers();

        if(count($users) == 0) {
            $trace->output("No details to be upserted.");
            return;
        }

        foreach($users as $user) {
            $this->upsertUser($trace, $user);
        }
    }

    private function upsertUser(progress_trace $trace, mdl_user $data) {
        $user = $this->userRepo->getByUsername($data->username);

        if($user)
        {
            if($updatedUser = $this->getUpdatedUser($user, $data))
            {
                $this->userRepo->update($updatedUser);
                $trace->output("User updated : $data->username ($data->email)");
            }
            else {
                $trace->output("No updates detected : $data->username ($data->email)");
            }

        }
        else {
            $this->userRepo->create($data);
            $trace->output("User created : $data->username ($data->email)");
        }
    }

    private function getUpdatedUser(mdl_user $current, mdl_user $new) {
        $hasChanges = false;

        if($current->username !== $new->username) {
            $current->username = $new->username;
            echo "username changed";
            $hasChanges = true;
        }

        if($current->firstname !== $new->firstname) {
            $current->firstname = $new->firstname;
            echo "firstname changed";
            $hasChanges = true;
        }

        if($current->lastname !== $new->lastname) {
            $current->lastname = $new->lastname;
            echo "lastname changed";
            $hasChanges = true;
        }

        if($current->email !== $new->email) {
            $current->email = $new->email;
            echo "email changed";
            $hasChanges = true;
        }

        if($current->getCustomData()->personGuid !== $new->getCustomData()->personGuid) {
            $current->getCustomData()->personGuid = $new->getCustomData()->personGuid;
            echo "personGuid changed";
            $hasChanges = true;
        }

        if($current->getCustomData()->pidm !== $new->getCustomData()->pidm) {
            $current->getCustomData()->pidm = $new->getCustomData()->pidm;
            echo "pidm changed";
            $hasChanges = true;
        }

        if($current->getCustomData()->userType !== $new->getCustomData()->userType) {
            $current->getCustomData()->userType = $new->getCustomData()->userType;
            echo "userType changed";
            $hasChanges = true;
        }

        if($new->getCustomData()->userType == "staff"){
            if($current->getCustomData()->isAdviserFlag !== $new->getCustomData()->isAdviserFlag) {
                $current->getCustomData()->isAdviserFlag = $new->getCustomData()->isAdviserFlag;
                echo "isAdviserFlag changed";
                $hasChanges = true;
            }

            if($current->getCustomData()->isModuleLeadFlag !== $new->getCustomData()->isModuleLeadFlag) {
                $current->getCustomData()->isModuleLeadFlag = $new->getCustomData()->isModuleLeadFlag;
                echo "isModuleLeadFlag changed";
                $hasChanges = true;
            }
        }
        elseif (strcasecmp($new->getCustomData()->userType, "student") == 0){
            if($current->getCustomData()->personHolds !== $new->getCustomData()->personHolds) {
                $current->getCustomData()->personHolds = $new->getCustomData()->personHolds;
                echo "personHolds changed";
                $hasChanges = true;
            }

            if($current->getCustomData()->serviceNeeds !== $new->getCustomData()->serviceNeeds) {
                $current->getCustomData()->serviceNeeds = $new->getCustomData()->serviceNeeds;
                echo "serviceNeeds changed";
                $hasChanges = true;
            }

            if($current->getCustomData()->studentGuid !== $new->getCustomData()->studentGuid) {
                $current->getCustomData()->studentGuid = $new->getCustomData()->studentGuid;
                echo "studentGuid changed";
                $hasChanges = true;
            }

            if($current->getCustomData()->studentAdviser !== $new->getCustomData()->studentAdviser) {
                $current->getCustomData()->studentAdviser = $new->getCustomData()->studentAdviser;
                echo "studentAdviser changed";
                $hasChanges = true;
            }

            if($current->getCustomData()->studentCompletionDate != $new->getCustomData()->studentCompletionDate) {
                echo "studentCompletionDate changed";
                $current->getCustomData()->studentCompletionDate = $new->getCustomData()->studentCompletionDate;
                $hasChanges = true;
            }

            if($current->getCustomData()->studentAcademicPrograms !== $new->getCustomData()->studentAcademicPrograms) {
                $current->getCustomData()->studentAcademicPrograms = $new->getCustomData()->studentAcademicPrograms;
                echo "studentAcademicPrograms changed";
                $hasChanges = true;
            }

            if($current->getCustomData()->studentStatus !== $new->getCustomData()->studentStatus) {
                $current->getCustomData()->studentStatus = $new->getCustomData()->studentStatus;
                echo "studentStatus changed";
                $hasChanges = true;
            }
        }

        if($hasChanges) {
            return $current;
        }

        return false;
    }

    public function getCustomData(int $id) : mdl_user_profile {
        $customDataRaw = $this->userRepo->getUserProfileData($id);

        $customData = new mdl_user_profile();
        $customData->populateObject($customDataRaw);

        return $customData;
    }

}