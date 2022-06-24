<?php

namespace enrol_ethos\ethosclient\service;

use enrol_ethos\ethosclient\entities\program_info;

class curriculum_lookup_service {

    private ethos_academic_program_service $academicProgramService;
    private ethos_educational_institution_unit_service $institutionService;
    private ethos_academic_level_service $academicLevelService;

    public function __construct() {
        $this->academicProgramService = ethos_academic_program_service::getInstance();
        $this->institutionService = ethos_educational_institution_unit_service::getInstance();
        $this->academicLevelService = ethos_academic_level_service::getInstance();
    }

    /**
     * @return program_info[]
     */
    public function getAllAcademicPrograms() : array {
        $ethosResults = $this->academicProgramService->getAll();

        $academicPrograms = array();

        foreach ($ethosResults as $ethosAcademicProgram) {
            $program = $this->convertEthosAcademicProgram($ethosAcademicProgram);
            array_push($academicPrograms, $program);
        }

        return $academicPrograms;
    }

    private function convertEthosAcademicProgram($academicProgram) : program_info {
        $programInfo = new program_info();


        if (isset($academicProgram->authorizing->institutionalUnit->id)) {
            $faculty = $this->institutionService->get($academicProgram->authorizing->institutionalUnit->id);
            $programInfo->facultyCode = $faculty->code;
            $programInfo->facultyTitle = $faculty->title;
        }

        if (isset($academicProgram->academicLevel->id)) {
            $academicLevel = $this->academicLevelService->get($academicProgram->academicLevel->id);
            $programInfo->academicLevelCode = $academicLevel->code;
            $programInfo->academicLevelTitle = $academicLevel->title;
        }

        $programInfo->guid = $academicProgram->id;
        $programInfo->courseCode = $academicProgram->code;
        $programInfo->courseTitle = $academicProgram->title;

        /** Dig out the period enrollment status stuff */
        //$startingPeriodId = $studentAcademicProgram->academicPeriods->starting->id;
        //$periodProfiles = $this->ethosClient->getAcademicPeriodProfiles($personId, $startingPeriodId);

        /** Dig out the student registration eligibility */
        // don't need this yet, but it works->
        //val eligibility = ethosClient->getStudentRegistrationEligibility(personId, startingPeriodId)


        /*
        if (count($periodProfiles)) {
            $enrollmentStatus = $this->ethosClient->getEnrollmentStatus($periodProfiles[0]->academicPeriodEnrollmentStatus->id);
            $programInfo->periodProfileEnrollmentStatusCode = $enrollmentStatus->code;
            $programInfo->periodProfileEnrollmentStatusTitle = $enrollmentStatus->title;
        }
        */
        /*
        $startOn = $this->ArrayToDateTime(date_parse($period->startOn));
        $endOn = $this->ArrayToDateTime(date_parse($period->endOn));

        var_dump($period);
        $programInfo->periodInfo = new period_info($period->category->type, $period->category->parent->academicPeriod->id, $period->code, $period->title, $endOn, $period->id, $startOn, $period->registration);
*/
                /*
        $programInfo->disciplines = $studentAcademicProgram->disciplines->stream()->map { d -> getDisciplineInfo(d) }?->collect(Collectors->toList())
        $programInfo->honours = $studentAcademicProgram->recognitions->stream()->map { r -> getHonoursInfo(r) }?->collect(Collectors->toList())
                */


        /*
        foreach ($studentAcademicProgram->disciplines as $discipline) {
            $programInfo->disciplines[] = $this->getDisciplineInfo($discipline->discipline->id);
        }
        */
        /*
        $awardCredential = count($studentAcademicProgram->credentials) ? $studentAcademicProgram->credentials[0] : null;

        if ($awardCredential!=null) {
            $award = $this->ethosClient->getAcademicCredential($awardCredential->id);
            $programInfo->awardAbbreviation = $award->abbreviation;
            $programInfo->awardTitle = $award->title;
            $programInfo->awardType = $award->type;
        }
        */
        return $programInfo;
    }
}