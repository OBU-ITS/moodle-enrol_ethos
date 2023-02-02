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
        return $this->userRepo->getUsersByAuthType("ldap", $limit, $offset);
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
            if($updatedUser = $this->getUpdatedUser($trace, $user, $data))
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

    private function getUpdatedUser(progress_trace $trace, mdl_user $current, mdl_user $new) {
        $hasChanges = false;

        if($current->username !== $new->username) {
            $current->username = $new->username;
            $trace->output("username changed (old: $current->username, new: $new->username)");
            $hasChanges = true;
        }

        if($current->firstname !== $new->firstname) {
            $current->firstname = $new->firstname;
            $trace->output("firstname changed (old: $current->firstname, new: $new->firstname)");
            $hasChanges = true;
        }

        if($current->lastname !== $new->lastname) {
            $current->lastname = $new->lastname;
            $trace->output("lastname changed (old: $current->lastname, new: $new->lastname)");
            $hasChanges = true;
        }

        if($current->email !== $new->email) {
            $current->email = $new->email;
            $trace->output("email changed (old: $current->email, new: $new->email)");
            $hasChanges = true;
        }

        if($current->getCustomData()->personGuid !== $new->getCustomData()->personGuid) {
            $current->getCustomData()->personGuid = $new->getCustomData()->personGuid;
            $trace->output("personGuid changed (old: {$current->getCustomData()->personGuid}, new: {$new->getCustomData()->personGuid})");
            $hasChanges = true;
        }

        if($current->getCustomData()->pidm !== $new->getCustomData()->pidm) {
            $current->getCustomData()->pidm = $new->getCustomData()->pidm;
            $trace->output("pidm changed (old: {$current->getCustomData()->pidm}, new: {$new->getCustomData()->pidm})");
            $hasChanges = true;
        }

        if($current->getCustomData()->userType !== $new->getCustomData()->userType) {
            $current->getCustomData()->userType = $new->getCustomData()->userType;
            $trace->output("userType changed (old: {$current->getCustomData()->userType}, new: {$new->getCustomData()->userType})");
            $hasChanges = true;
        }

        if($new->getCustomData()->userType == "staff"){
            if($current->getCustomData()->isAdviserFlag !== $new->getCustomData()->isAdviserFlag) {
                $current->getCustomData()->isAdviserFlag = $new->getCustomData()->isAdviserFlag;
                $trace->output("isAdviserFlag changed (old: {$current->getCustomData()->isAdviserFlag}, new: {$new->getCustomData()->isAdviserFlag})");
                $hasChanges = true;
            }

            if($current->getCustomData()->isModuleLeadFlag !== $new->getCustomData()->isModuleLeadFlag) {
                $current->getCustomData()->isModuleLeadFlag = $new->getCustomData()->isModuleLeadFlag;
                $trace->output("isModuleLeadFlag changed (old: {$current->getCustomData()->isModuleLeadFlag}, new: {$new->getCustomData()->isModuleLeadFlag})");
                $hasChanges = true;
            }
        }
        elseif (strcasecmp($new->getCustomData()->userType, "student") == 0){
            if($current->getCustomData()->personHolds !== $new->getCustomData()->personHolds) {
                $current->getCustomData()->personHolds = $new->getCustomData()->personHolds;
                $trace->output("personHolds changed (old: {$current->getCustomData()->personHolds}, new: {$new->getCustomData()->personHolds})");
                $hasChanges = true;
            }

            if($current->getCustomData()->serviceNeeds !== $new->getCustomData()->serviceNeeds) {
                $current->getCustomData()->serviceNeeds = $new->getCustomData()->serviceNeeds;
                $trace->output("serviceNeeds changed (old: {$current->getCustomData()->serviceNeeds}, new: {$new->getCustomData()->serviceNeeds})");
                $hasChanges = true;
            }

            if($current->getCustomData()->studentGuid !== $new->getCustomData()->studentGuid) {
                $current->getCustomData()->studentGuid = $new->getCustomData()->studentGuid;
                $trace->output("studentGuid changed (old: {$current->getCustomData()->studentGuid}, new: {$new->getCustomData()->studentGuid})");
                $hasChanges = true;
            }

            if($current->getCustomData()->studentAdviser !== $new->getCustomData()->studentAdviser) {
                $current->getCustomData()->studentAdviser = $new->getCustomData()->studentAdviser;
                $trace->output("studentAdviser changed (old: {$current->getCustomData()->studentAdviser}, new: {$new->getCustomData()->studentAdviser})");
                $hasChanges = true;
            }

            if($new->getCustomData()->studentCompletionDate == '') {
                $new->getCustomData()->studentCompletionDate = 0;
            }
            if($current->getCustomData()->studentCompletionDate != $new->getCustomData()->studentCompletionDate) {
                $current->getCustomData()->studentCompletionDate = $new->getCustomData()->studentCompletionDate;
                $trace->output("studentCompletionDate changed (old: {$current->getCustomData()->studentCompletionDate}, new: {$new->getCustomData()->studentCompletionDate})");
                $hasChanges = true;
            }

            if($current->getCustomData()->studentAcademicPrograms !== $new->getCustomData()->studentAcademicPrograms) {
                $current->getCustomData()->studentAcademicPrograms = $new->getCustomData()->studentAcademicPrograms;
                $trace->output("studentAcademicPrograms changed (old: {$current->getCustomData()->studentAcademicPrograms}, new: {$new->getCustomData()->studentAcademicPrograms})");
                $hasChanges = true;
            }

            if($current->getCustomData()->studentStatus !== $new->getCustomData()->studentStatus) {
                $current->getCustomData()->studentStatus = $new->getCustomData()->studentStatus;
                $trace->output("studentStatus changed (old: {$current->getCustomData()->studentStatus}, new: {$new->getCustomData()->studentStatus})");
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