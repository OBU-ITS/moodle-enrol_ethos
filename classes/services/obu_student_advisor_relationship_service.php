<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_user;
use enrol_ethos\entities\profileFields\obu_student_advisor_relationship;
use enrol_ethos\ethosclient\entities\ethos_student_advisor_relationship_info;

class obu_student_advisor_relationship_service
{
    const ACTIVATION_ADVANCE = "Today +60 days";

    private function __construct()
    {
    }

    private static ?obu_student_advisor_relationship_service $instance = null;
    public static function getInstance(): obu_student_advisor_relationship_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param mdl_user $user
     */
    public function cleanStudentAdvisorProfileField(mdl_user $user) {
        $pendingStudentAdvisorRelationships = $this->deserialize($user->getCustomData()->studentAdvisers);
        $activeAdvisorRelationships = $this->deserialize($user->getCustomData()->studentAdviser);

        $newPendingStudentAdvisorRelationships = array();
        foreach($pendingStudentAdvisorRelationships as $pendingStudentAdvisorRelationship) {
            if($this->isStudentAdvisorRelationshipActive($pendingStudentAdvisorRelationship)) {
                $activeAdvisorRelationships[] = $pendingStudentAdvisorRelationship;
            }
            else {
                $newPendingStudentAdvisorRelationships[] = $pendingStudentAdvisorRelationship;
            }
        }

        $user->getCustomData()->studentAdvisers = $this->serialize($newPendingStudentAdvisorRelationships);
        $user->getCustomData()->studentAdviser = $this->serialize($activeAdvisorRelationships);
    }

    /**
     * @param ethos_student_advisor_relationship_info[] $studentAdvisorRelationshipInfos
     */
    public function setStudentAdvisorRelationships(mdl_user $user, array $studentAdvisorRelationshipInfos) {
        $pendingStudentAdvisorRelationships = array();
        $activeStudentAdvisorRelationships = array();
        foreach($studentAdvisorRelationshipInfos as $studentAdvisorRelationshipInfo) {
            $studentAdvisorRelationship = new obu_student_advisor_relationship();
            $studentAdvisorRelationship->populateObjectByEthosInfo($studentAdvisorRelationshipInfo);
            if($this->isStudentAdvisorRelationshipActive($studentAdvisorRelationship)) {
                $activeStudentAdvisorRelationships[] = $studentAdvisorRelationship;
            }
            else {
                $pendingStudentAdvisorRelationships[] = $studentAdvisorRelationship;
            }
        }

        $user->getCustomData()->studentAdvisers = $this->serialize($pendingStudentAdvisorRelationships);
        $user->getCustomData()->studentAdviser = $this->serialize($activeStudentAdvisorRelationships);
    }

    /**
     * @param ethos_student_advisor_relationship_info[] $studentAdvisorRelationshipInfos
     * @return bool
     */
    public function anyStudentAdvisorRelationshipsActive(array $studentAdvisorRelationshipInfos) : bool {
        foreach($studentAdvisorRelationshipInfos as $studentAdvisorRelationshipInfo) {
            $studentAdvisorRelationship = new obu_student_advisor_relationship();
            $studentAdvisorRelationship->populateObjectByEthosInfo($studentAdvisorRelationshipInfo);
            if($this->isStudentAdvisorRelationshipActive($studentAdvisorRelationship)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $items
     * @return obu_student_advisor_relationship[]
     */
    private function deserialize(string $items) : array {
        $data = json_decode($items);

        if(!is_array($data)) {
            return array();
        }

        return array_map(function ($item) {
            $hold = new obu_student_advisor_relationship();
            $hold->populateObject($item);
            return $hold;
        }, $data);
    }

    /**
     * @param obu_student_advisor_relationship[] $items
     * @return string
     */
    private function serialize(array $items) : string
    {
        return count($items) == 0
            ? ""
            : json_encode($items);
    }

    /**
     * @param obu_student_advisor_relationship $studentAdvisorRelationship
     * @return bool
     */
    private function isStudentAdvisorRelationshipActive(obu_student_advisor_relationship $studentAdvisorRelationship) : bool {
        $startDate = strtotime($studentAdvisorRelationship->startOn);
        $activeDate = strtotime(self::ACTIVATION_ADVANCE);

        return ($startDate < $activeDate);
    }
}