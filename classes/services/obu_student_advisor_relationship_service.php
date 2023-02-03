<?php
namespace enrol_ethos\services;

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
     * @param ethos_student_advisor_relationship_info[] $studentAdvisorRelationships
     */
    public function replaceStudentAdvisorRelationships(array $studentAdvisorRelationships) {

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
}