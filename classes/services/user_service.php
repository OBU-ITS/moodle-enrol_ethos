<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\client\ethos_client;
use enrol_ethos\interfaces\user_repository_interface;
use enrol_ethos\services\course_service;
use enrol_ethos\entities\user;
use enrol_ethos\entities\user_profile;
use enrol_ethos\entities\enrolment;

class user_service {
    const BANNER_GUID = 'bannerGuid';
    private $userRepository;
    private $courseService;

    public function __construct(user_repository_interface $userRepository, course_service $courseService) {
        $this->userRepository = $userRepository;
        $this->courseService = $courseService;
    }

    public function getAlumniDuration() {
        return date('Y-m-d', strtotime("+1 year"));
    }

    private function processAlumniEnrolment(user $user) {
        // Alumni course

        $alumniCourseIdNumber = get_config('enrol_ethos', 'alumnicourseidnumber');

        if ($alumniCourseIdNumber) {
            if ($user->userProfile->endDate < $this->getAlumniDuration()) {

                $course = $this->courseService->getCourseById($alumniCourseIdNumber);

                if ($course) {
                    $enrolment = new enrolment();
                    $enrolment->course = $course;
                    array_push($user->enrolments, $enrolment);
                }
            }
        }
    }

    private function processDisabilityEnrolment(user $user) {
        // Alumni course

        $disabilityCourseIdNumber = get_config('enrol_ethos', 'disabilitycourseidnumber');

        if ($disabilityCourseIdNumber) {
            if ($user->userProfile->dyslexic) {

                $course = $this->courseService->getCourseById($disabilityCourseIdNumber);

                if ($course) {
                    $enrolment = new enrolment();
                    $enrolment->course = $course;
                    array_push($user->enrolments, $enrolment);
                }
            }
        }
    }

    private function processLeadProgramme(user $user) {
        // Lead programme
        $course = $this->courseService->getCourseByShortName($user->userProfile->courseCode);

        if ($course) {
            $enrolment = new enrolment();
            $enrolment->course = $course;

            array_push($user->enrolments, $enrolment);
        }
    }

    public function addDefaultEnrolments(user $user) {
        $this->processAlumniEnrolment($user);

        $this->processLeadProgramme($user);

        $this->processDisabilityEnrolment($user);
    }

    public function updateUserProfile(user $user) {
        $this->userRepository->save($user);
    }

    public function createUser(string $username, string $firstname, string $lastname, string $email) : int {
        $id = $this->userRepository->createUser($username, $firstname, $lastname, $email);

        return $this->getUserById($id);
    }

    public function getUserProfilesWithBannerId(): array
    {
        $profileField = self::BANNER_GUID;
        $dbusers = $this->userRepository->getAllUsersWithProfileFieldData($profileField);
        return($this->convertUserProfiles($dbusers,$profileField));
    }

    public function getUserProfilesWithBannerIds($bannerIds): array
    {
        $profileField = self::BANNER_GUID;
        $dbusers = $this->userRepository->getUsersByProfileField($profileField,$bannerIds);
        return($this->convertUserProfiles($dbusers,$profileField,$bannerIds));
    }

    public function getUserProfilesWithoutBannerIds() {
        $profileField = self::BANNER_GUID;
        $dbusers = $this->userRepository->getUsersWithoutProfileFieldData($profileField);
        return($this->convertUserProfiles($dbusers,null));
    }

    public function getAllUsers() {
        $dbusers = $this->userRepository->getAllUsers();
        return($this->convertUserProfiles($dbusers));
    }

    public function getUsersByAuthType($authType) {
        $dbusers = $this->userRepository->getUsersByAuthType($authType);
        return($this->convertUserProfiles($dbusers));
    }

    public function getUserById($id){
        $dbuser = $this->userRepository->getById($id);
        return($this->convertUserProfile($dbuser));
    }

    public function getUserByUsername($username){
        $dbuser = $this->userRepository->getByUsername($username);
        return($this->convertUserProfile($dbuser));
    }

    private function convertUserProfiles($dbusers, $profileField = null) {
        $users = array();

        foreach ($dbusers as $dbuser) {
            $user = $this->convertUserProfile($dbuser, $profileField);
            array_push($users, $user);
        }

        return $users;
    }

    private function convertUserProfile($dbuser, $profileField = null) {
        $userProfile = new user_profile();
        if ($profileField) {
            $userProfile->$profileField = $dbuser->data;
        }
        $user = new user($dbuser->userid, $userProfile);
        $user->username = $dbuser->username;

        return $user;
    }
}