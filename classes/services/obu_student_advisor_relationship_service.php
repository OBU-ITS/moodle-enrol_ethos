<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\mdl_user;
use enrol_ethos\entities\profileFields\obu_student_advisor_relationship;
use enrol_ethos\ethosclient\entities\ethos_student_advisor_relationship_info;

class obu_student_advisor_relationship_service
{
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
     * @param obu_student_advisor_relationship[] $studentAdvisorRelationships
     */
    public function replaceStudentAdvisorRelationships(array $studentAdvisorRelationships) : string{
        foreach ($studentAdvisorRelationships as $studentAdvisersJson) {
            //convert start on for each studentadviserjson to date and check how many days till start on
            $startDate = strtotime($studentAdvisersJson->startOn);
            $daysToStartDate = ceil(($startDate - time()) / 60 / 60 / 24);
            //if less than 60 days till start on then add studentadvisor to profilefield for user
            if ($daysToStartDate < 60) {
                //still working on this bit
                $this->replaceStudentAdvisorRelationships($studentAdvisersJson);
            }
        }

        return "";
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
    public function serialize(array $items) : string
    {
        return count($items) == 0
            ? ""
            : json_encode($items);
    }

    /**
     * @param string $studentAdvisers
     * @return string
     */
    public function cleanStudentAdvisorProfileField(string $studentAdvisers) : string {
        $studentAdvisersJsons = $this->deserialize($studentAdvisers);
        return $this->replaceStudentAdvisorRelationships($studentAdvisersJsons);
    }
}