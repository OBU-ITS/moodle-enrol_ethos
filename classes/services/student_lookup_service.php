<?php
namespace enrol_ethos\services;

use DateTime;
use enrol_ethos\ethosclient\entities\discipline_info;
use enrol_ethos\ethosclient\entities\name_info;
use enrol_ethos\ethosclient\entities\period_info;
use enrol_ethos\ethosclient\entities\program_info;
use enrol_ethos\ethosclient\entities\student_info;
use enrol_ethos\ethosclient\service\ethos_academic_credential_provider;
use enrol_ethos\ethosclient\service\ethos_academic_discipline_provider;
use enrol_ethos\ethosclient\service\ethos_academic_level_provider;
use enrol_ethos\ethosclient\service\ethos_academic_period_provider;
use enrol_ethos\ethosclient\service\ethos_academic_program_provider;
use enrol_ethos\ethosclient\service\ethos_educational_institution_unit_provider;
use enrol_ethos\ethosclient\service\ethos_person_provider;
use enrol_ethos\ethosclient\service\ethos_site_provider;
use enrol_ethos\ethosclient\service\ethos_student_academic_period_provider;
use enrol_ethos\ethosclient\service\ethos_student_academic_period_status_provider;
use enrol_ethos\ethosclient\service\ethos_student_academic_program_provider;
use enrol_ethos\ethosclient\service\ethos_student_service;
use enrol_ethos\ethosclient\service\ethos_student_status_provider;
use enrol_ethos\ethosclient\service\ethos_student_type_provider;

class student_lookup_service {
    private ethos_person_provider $personService;
    private ethos_student_service $studentService;
    private ethos_student_type_provider $studentTypeService;
    private ethos_student_status_provider $studentStatusService;
    private ethos_student_academic_program_provider $studentAcademicProgramService;
    private ethos_academic_period_provider $academicPeriodService;
    private ethos_site_provider $siteService;
    private ethos_educational_institution_unit_provider $institutionService;
    private ethos_academic_program_provider $academicProgramService;
    private ethos_academic_level_provider $academicLevelService;
    private ethos_student_academic_period_provider $studentAcademicPeriodService;
    private ethos_academic_credential_provider $academicCredentialService;
    private ethos_student_academic_period_status_provider $studentAcademicPeriodStatusService;
    private ethos_academic_discipline_provider $academicDisciplineService;

    var $trace;

    public function __construct($trace=null) {
        $this->personService = ethos_person_provider::getInstance();
        $this->studentService = ethos_student_service::getInstance();
        $this->studentTypeService = ethos_student_type_provider::getInstance();
        $this->studentStatusService = ethos_student_status_provider::getInstance();
        $this->studentAcademicProgramService = ethos_student_academic_program_provider::getInstance();
        $this->academicPeriodService = ethos_academic_period_provider::getInstance();
        $this->siteService = ethos_site_provider::getInstance();
        $this->institutionService = ethos_educational_institution_unit_provider::getInstance();
        $this->academicProgramService = ethos_academic_program_provider::getInstance();
        $this->academicLevelService = ethos_academic_level_provider::getInstance();
        $this->studentAcademicPeriodService = ethos_student_academic_period_provider::getInstance();
        $this->academicCredentialService = ethos_academic_credential_provider::getInstance();
        $this->studentAcademicPeriodStatusService = ethos_student_academic_period_status_provider::getInstance();
        $this->academicDisciplineService = ethos_academic_discipline_provider::getInstance();

        $this->trace = $trace;
    }

    private function ArrayToDateTime($dateField) {
        $dateString = $dateField['year'] . '-' .$dateField['month'] . '-' . $dateField['day'];
        return DateTime::createFromFormat('Y-m-d', $dateString);
    }

    private function log($message){
        if ($this->trace) {
            $this->trace->output($message);
        }
    }

    public function lookupStudentFromPersonId($bannerId) {

        $person = $this->personService->get($bannerId);

        return $this->lookupStudent($person);
    }

    public function lookupStudentFromBannerId($bannerId) {

        $start_time = microtime(true);
        $persons = $this->personService->getPersonsByBannerId($bannerId);
        $end_time = microtime(true);
        $time = $end_time-$start_time;
        $this->log("Ethos person lookup took $time seconds");
        if($persons) {
            $this->log(count($persons) . " Ethos persons found");
        }
        else{
            $this->log("No Ethos persons found");
        }

        if ($persons && count($persons) === 1) {
            return $this->lookupStudent($persons[0]);
        }

        return null;
    }

    /**
     * Get summary student information for the person specified person
     *
     */
    public function lookupStudent($person) {
        if (!$person || isset($person->errors)) {
            $this->log("lookupStudent: No person OR errors is set");
            if(isset($person->errors)) {
                foreach ($person->errors as $error) {
                    $this->log($error);
                }
            }
            return false;
        }

        //logger->info("Looking up a student with person id %s"->format(personId))
        $this->log("Looking up a student with person id: " . $person->id);

        $newStudent = new student_info($person->id);

        $start_time = microtime(true);
        $students = $this->studentService->getStudentByPersonId($newStudent->personId);
        $end_time = microtime(true);
        $time = $end_time-$start_time;
        $this->log("Ethos student lookup took $time seconds");

        if (!$students || (count($students) != 1)) {
            return false;
        }

        $student = $students[0];
        $studentAcademicPrograms = $this->studentAcademicProgramService->getStudentAcademicProgramsByPersonId($newStudent->personId);

        // load in all of the academic programs
        $newStudent->programmes = array(); //studentAcademicPrograms->stream()->map { p -> getProgramInfo(personId, p) }->collect(Collectors->toList())

        foreach ($studentAcademicPrograms as $studentAcademicProgram) {
            $program = $this->getProgramInfo($newStudent->personId, $studentAcademicProgram);
            $newStudent->programmes[] = $program;
        }

        $nameInfo = $this->getName($person);

        // TODO: WHY IS THIS MISSING IN BILD FOR API V7?
        //$newStudent->studentId = $student->id;

        //$newStudent->studentNumber = $person->credentials->filter { credential -> credential->type->equals("bannerId") }?->first()?->value

        $newStudent->forename = $nameInfo->firstName;
        $newStudent->surname = $nameInfo->lastName;
        $newStudent->middleName = $nameInfo->middleName;
        $newStudent->title = $nameInfo->prefix;
        $newStudent->nickName = $nameInfo->nickName;

        if (isset($student->dyslexic)) {
            $newStudent->dyslexic = ($student->dyslexic == true);
        }

        if (isset($person->dateOfBirth)) {
            $newStudent->dateOfBirth = $this->ArrayToDateTime(date_parse($person->dateOfBirth)) ?? null;
        }

        if (isset($student->type) && $attendanceMode = $this->studentTypeService->get($student->type->id)) {
            $newStudent->attendanceMode = $attendanceMode->code;
            $newStudent->attendanceModeTitle = $attendanceMode->title;
        }

        if (isset($student->status) && $statusObj = $this->studentStatusService->get($student->status->id)) {
            $newStudent->status = $statusObj->code;
            $newStudent->statusTitle = $statusObj->title;
        }

        //TODO
        //$leadProgramOfStudy = $programmeSelector->getRelevantProgramme($newStudent->programmes);

        if ($leadProgramOfStudy = $newStudent->programmes[0] ?? null) {
            $newStudent->leadProgramOfStudy = $leadProgramOfStudy;
            $newStudent->courseCode = $leadProgramOfStudy->courseCode;
            $newStudent->courseTitle = $leadProgramOfStudy->courseTitle;
            $newStudent->facultyCode = $leadProgramOfStudy->facultyCode;
            $newStudent->facultyTitle = $leadProgramOfStudy->facultyTitle;
            $newStudent->schoolTypeCode = $leadProgramOfStudy->schoolTypeCode;
            $newStudent->schoolTypeTitle = $leadProgramOfStudy->schoolTypeTitle;
            $newStudent->academicLevel = $leadProgramOfStudy->schoolTypeTitle;
            $newStudent->startDate = $leadProgramOfStudy->startOn;
            $newStudent->endDate = $leadProgramOfStudy->endOn;
            $newStudent->graduatedOn = $leadProgramOfStudy->graduatedOn;
            $newStudent->creditsEarned = $leadProgramOfStudy->creditsEarned;
            // Set the lead award code and title
            $newStudent->awardCode = $leadProgramOfStudy->awardAbbreviation;
            $newStudent->awardTitle = $leadProgramOfStudy->awardTitle;

        }

        // Get the first major
        //$disciplineMajor = null;//leadProgramOfStudy?->disciplines?->stream()?->filter( { d-> d->disciplineType->equals("major")} )?->collect(Collectors->toList())?->firstOrNull()

        // If there is a major discipline code, set it

        if ($disciplineMajor = array_values(array_filter($leadProgramOfStudy->disciplines, function ($a) {
            return $a->disciplineType == "major";
        }))[0] ?? null) {
            $newStudent->subjectCode = $disciplineMajor->disciplineCode;
            $newStudent->subjectTitle = $disciplineMajor->disciplineTitle;
        }

        // Get the period profile for the leadProgramOfStudy
        //var periodProfile = ethosClient->getAcademicPeriodProfile(personId, leadProgramOfStudy->)

        //TODO
        //logger->info("Student info recovered: " + newStudent->toString())

        return $newStudent;
    }

    /**
     * Looks up the full academic program info for the StudentAcademicProgram
     */
    public function getProgramInfo($personId, $studentAcademicProgram) : program_info{

        $programInfo = new program_info();

        $period = $this->academicPeriodService->get($studentAcademicProgram->academicPeriods->starting->id);

        if ($studentAcademicProgram->site->id != null) {
            if ($site = $this->siteService->get($studentAcademicProgram->site->id)) {
                $programInfo->siteCode = $site->code;
                $programInfo->siteTitle = $site->title;
            }
        }

        if ($academicProgramLevel = $this->academicLevelService->get($studentAcademicProgram->academicLevel->id)) {
            $programInfo->schoolTypeCode = $academicProgramLevel->code;
            $programInfo->schoolTypeTitle = $academicProgramLevel->title;
        }

        if ($academicProgram = $this->academicProgramService->get($studentAcademicProgram->program->id)) {
            $programInfo->courseCode = $academicProgram->code;
            $programInfo->courseTitle = $academicProgram->title;

            if ($faculty = $this->institutionService->get($academicProgram->authorizing->institutionalUnit->id)) {
                $programInfo->facultyCode = $faculty->code;
                $programInfo->facultyTitle = $faculty->title;
            }

        }

        $programInfo->preference = $studentAcademicProgram->preference;
        $programInfo->startOn = $this->ArrayToDateTime(date_parse($studentAcademicProgram->startOn)) ?? null;

        //TODO - were these blank in the Ethos client?
        //$programInfo->endOn = $studentAcademicProgram->endOn;
        //$programInfo->graduatedOn = $studentAcademicProgram->graduatedOn;
        //$programInfo->creditsEarned = $studentAcademicProgram->creditsEarned;
        $programInfo->enrollmentStatus = $studentAcademicProgram->enrollmentStatus->status;

        /** Dig out the period enrollment status stuff */
        $startingPeriodId = $studentAcademicProgram->academicPeriods->starting->id;
        $periodProfiles = $this->studentAcademicPeriodService->getAcademicPeriodProfiles($personId, $startingPeriodId);

        /** Dig out the student registration eligibility */
        // don't need this yet, but it works->
        //val eligibilities = ethosClient->getStudentRegistrationEligibility(personId, startingPeriodId)

        if (count($periodProfiles)) {
            if ($enrollmentStatus = $this->studentAcademicPeriodStatusService->get($periodProfiles[0]->academicPeriodEnrollmentStatus->id)) {
                $programInfo->periodProfileEnrollmentStatusCode = $enrollmentStatus->code;
                $programInfo->periodProfileEnrollmentStatusTitle = $enrollmentStatus->title;
            }
        }

        $startOn = $this->ArrayToDateTime(date_parse($period->startOn));
        $endOn = $this->ArrayToDateTime(date_parse($period->endOn));

        if ($period) {
            $programInfo->periodInfo = new period_info($period->category->type, $period->category->parent->id, $period->code, $period->title, $endOn, $period->id, $startOn, $period->registration);
        }
                /*
        $programInfo->disciplines = $studentAcademicProgram->disciplines->stream()->map { d -> getDisciplineInfo(d) }?->collect(Collectors->toList())
        $programInfo->honours = $studentAcademicProgram->recognitions->stream()->map { r -> getHonoursInfo(r) }?->collect(Collectors->toList())
                */

        foreach ($studentAcademicProgram->disciplines as $discipline) {
            if ($discipline = $this->getDisciplineInfo($discipline->discipline->id)) {
                $programInfo->disciplines[] = $discipline;
            }
        }


        $awardCredential = count($studentAcademicProgram->credentials) ? $studentAcademicProgram->credentials[0] : null;

        if ($awardCredential!=null) {
            if ($award = $this->academicCredentialService->get($awardCredential->id)) {
                $programInfo->awardAbbreviation = $award->abbreviation;
                $programInfo->awardTitle = $award->title;
                $programInfo->awardType = $award->type;
            }
        }

        return $programInfo;
    }


    public function getName($person) : name_info {
        $names = $person->names;

        // get official name
        $officialName = array_filter($names, function ($a) { return !isset($a->type); })[0];

        // Get the preferred / nickname
        $preferredNameArray = array_values(array_filter($names, function ($a) { return ((isset($a->type)) && ($a->type->category == "favored")); }));

        $name = new name_info();
        //$name->prefix = $officialName->title;
        $name->firstName = $officialName->firstName ?? null;
        $name->lastName = $officialName->lastName ?? null;
        $name->middleName = $officialName->middleName ?? null;
        $name->fullName = $officialName->fullName ?? null;
        $name->nickName = count($preferredNameArray) ? $preferredNameArray[0]->firstName : null;

        return $name;
    }

    public function getDisciplineInfo($disciplineId) : ?discipline_info {
        // TODO ethosDisciplineId.administeringInstitutionUnit should contain the department for the subject of study
        if ($discipline = $this->academicDisciplineService->get($disciplineId)) {
            return new discipline_info($discipline->abbreviation, $discipline->type, $discipline->title);
        }

        return null;
    }


//    public function getStudentsWithChanges($lastProcessedID = 0, $maxProcessedID = 0, $processLimit = 4000): ethos_notifications
//    {
//        $this->log("Ethos consume started.");
//
//        $time_start = microtime(true);
//
//        $messagesModel = new ethos_notifications();
//        $processedCount = 0;
//        $maxProcessedIdReached = false;
//
//        do {
//            $messages = $this->ethosClient->consumeMessages($lastProcessedID);
//
//            $messagesCount = count($messages);
//            //$this->log("$messagesCount messages consumed from Ethos.");
//
//            foreach ($messages as $message) {
//                $messageId = $message->id;
//
//                // TODO : Remove maxProcessedID after testing. The following code will consume but not process any messages above a given Ethos message ID.
//                if($maxProcessedID > 0 && $messageId > $maxProcessedID){
//                    $maxProcessedIdReached = true;
//                    break;
//                }
//
//                $lastProcessedID = $messageId;
//
//                $messageModel = null;
//
//                $processedCount++;
//
//                if (isset($message->resource)
//                    && isset($message->content)
//                    && isset($message->operation)
//                    && ($message->operation !== 'deleted')) {
//
//                    $resourceName = $message->resource->name;
//                    $resourceId = $message->resource->id;
//                    $messageContent = $message->content;
//
//                    switch ($resourceName) {
//                        case 'persons':
//                        case 'person-holds':
//                            $messageModel = new ethos_notification($messageId, $resourceId, $messageContent->id);
//                            $messagesModel->addPerson($messageModel);
//                            break;
//                        case 'student-academic-period-profiles':
//                        case 'students':
//                            // TODO : Implement student update
//                            // $messageModel = new ethos_notification($messageId, $resourceId, $messageContent->person->id);
//                            break;
//                        case 'student-academic-programs':
//                            $messageModel = new ethos_notification($messageId, $resourceId, $messageContent->student->id);
//                            $messagesModel->addStudentAcademicPrograms($messageModel);
//                            break;
//                    }
//                }
//            }
//
//        } while (!$maxProcessedIdReached && $messagesCount > 0 && $processedCount < $processLimit);
//
//
//        $this->log("Found $processedCount messages.");
//        if($processedCount > 0) {
//            $personCount = count($messagesModel->persons);
//            $this->log("Found $personCount persons to process.");
//            $studentAcademicProgramsCount = count($messagesModel->studentAcademicPrograms);
//            $this->log("Found $studentAcademicProgramsCount student academic programs to process.");
//        }
//
//        $time_end = microtime(true);
//        $time = round($time_end - $time_start, 2, PHP_ROUND_HALF_UP);
//        if($maxProcessedIdReached) {
//            $this->log("Ethos consume finished in $time seconds: Max processed ID reached.");
//        }
//        else if($processedCount >= $processLimit) {
//            $this->log("Ethos consume finished in $time seconds: Process limit ($processLimit) reached. $processedCount messages processed.");
//        }
//        else {
//            $this->log("Ethos consume finished in $time seconds: All messages consumed.");
//        }
//
//        return $messagesModel;
//    }
}