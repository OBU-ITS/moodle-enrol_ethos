<?php

namespace enrol_ethos\services;

use enrol_ethos\ethosclient\entities\ethos_academic_period_info;

class obu_academic_period_service
{
    private function __construct()
    {
    }

    private static ?obu_academic_period_service $instance = null;
    public static function getInstance() : obu_academic_period_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param ethos_academic_period_info $subTerm
     * @return ethos_academic_period_info
     */
    public function getTerm(ethos_academic_period_info $subTerm) : ?ethos_academic_period_info {
        return $subTerm->category->getParent();
    }

    /**
     * @param ethos_academic_period_info $term
     * @return ethos_academic_period_info
     */
    public function getYear(ethos_academic_period_info $term) : ?ethos_academic_period_info {
        return $term->category->getParent();
    }
}