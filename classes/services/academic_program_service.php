<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\service\ethos_academic_program_provider;

class academic_program_service
{
    private ethos_academic_program_provider $academicProgramService;

    public function __construct()
    {
        $this->academicProgramService = ethos_academic_program_provider::getInstance();
    }

    public function getAllAcademicPrograms() : array {
        return $this->academicProgramService->getAll();
    }
}