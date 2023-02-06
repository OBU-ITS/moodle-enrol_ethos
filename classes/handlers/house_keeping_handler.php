<?php
namespace enrol_ethos\handlers;

use enrol_ethos\entities\obu_users_info;
use enrol_ethos\repositories\db_user_repository;
use enrol_ethos\services\moodle\mdl_user_service;
use enrol_ethos\services\obu_person_hold_service;
use enrol_ethos\services\obu_student_advisor_relationship_service;
use progress_trace;

class house_keeping_handler {
    private mdl_user_service $userService;
    private db_user_repository $userRepo;
    private obu_person_hold_service $personHoldsService;
    private obu_student_advisor_relationship_service $studentAdvisorRelationshipService;
    private progress_trace $trace;

    public function __construct($trace)
    {
        global $DB;
        $this->userRepo = new db_user_repository($DB);
        $this->userService = mdl_user_service::getInstance();
        $this->personHoldsService = obu_person_hold_service::getInstance();
        $this->studentAdvisorRelationshipService = obu_student_advisor_relationship_service::getInstance();
        $this->trace = $trace;
    }

    public function handlePersonHoldsCleaning() {
        $users = $this->userRepo->getAllUsersWithProfileFieldData("person_holds");

        $updatedUsers = new obu_users_info();
        foreach($users as $user) {
            $user->getCustomData()->personHolds = $this->personHoldsService->cleanHoldsProfileField($user->getCustomData()->personHolds);
            $updatedUsers->addUser($user);
        }

        $this->userService->handleUserCreation($this->trace, $updatedUsers);
    }

    //look at json in new profile field for student_advisers,
    //find anything with a start on date less than 60 days in the future, ensure that student_adviser field has a comma seperated name
    public function handleStudentAdviserRelationships()
    {
        $users = $this->userRepo->getAllUsersWithProfileFieldData("student_advisers"); //TODO is this too much

        $updatedUsers = new obu_users_info();
        foreach ($users as $user) {
            $user->getCustomData()->studentAdviser = $this->studentAdvisorRelationshipService->cleanStudentAdvisorProfileField($user->getCustomData()->studentAdviser);
            $updatedUsers->addUser($user);
        }

        $this->userService->handleUserCreation($this->trace, $updatedUsers);
    }
}