<?php
namespace enrol_ethos\services;


use enrol_ethos\entities\obu_course_hierarchy_info;

class obu_program_service
{
    private function __construct()
    {
        // TODO
    }

    private static ?obu_program_service $instance = null;
    public static function getInstance(): obu_program_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(obu_course_hierarchy_info $hierarchy, string $id) {
        // TODO
    }

    public function getBatch(obu_course_hierarchy_info $hierarchy, $limit, $offset) : int {
        // TODO

        return 0; // TODO
    }

    public function getAll(obu_course_hierarchy_info $hierarchy) {
        // TODO
    }
}

