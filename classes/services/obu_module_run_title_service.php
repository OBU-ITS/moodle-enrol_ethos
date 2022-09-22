<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\entities\ethos_section_info_title;

class obu_module_run_title_service
{
    private function __construct()
    {
    }

    private static ?obu_module_run_title_service $instance = null;
    public static function getInstance() : obu_module_run_title_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param ethos_section_info_title[] $titles
     * @return string
     */
    public function getShortTitle(array $titles) : string {
        foreach($titles as $title) {
            $type = $title->getType();

            if($type->code == "short" || $type->code == "courseShort") {
                return $title->value;
            }
        }
    }

    /**
     * @param ethos_section_info_title[] $titles
     * @return string
     */
    public function getLongTitle(array $titles) : string {
        foreach($titles as $title) {
            $type = $title->getType();

            if($type->code == "long" || $type->code == "courseLong") {
                return $title->value;
            }
        }
    }
}