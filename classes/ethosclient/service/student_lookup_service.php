<?php

namespace enrol_ethos\ethosclient\service;

class student_lookup_service {

    var $ethosClient;
    var $trace;

    var $dateFormat = 'Y-m-d\TH:i:sZ';

    public function __construct($ethosClient, $trace=null) {
        $this->ethosClient = $ethosClient;
        $this->trace = $trace;
    }

    private function ArrayToDateTime($dateField) {
        $datestring = $dateField['year'] . '-' .$dateField['month'] . '-' . $dateField['day'];
        return \DateTime::createFromFormat('Y-m-d', $datestring);
    }

    private function log($message){
        if ($this->trace) {
            $this->trace->output($message);
        }
    }

    public function lookupStudentFromPersonId($bannerId) {
        //logger.info("Looking up a student with student id %s".format(studentId))

        $person = $this->ethosClient->getPersonById($bannerId);

        return $this->lookupStudent($person);
    }

    public function lookupStudentFromBannerId($bannerId) {
        
        if ($GLOBALS['alluser-debug']) $this->log("LOOKING UP $bannerId");

        $persons = $this->ethosClient->getPersonsByBannerId($bannerId);

        //TODO: REMOVE
        if ($GLOBALS['alluser-debug']) $this->log(var_dump($persons));

        if ($persons && count($persons) === 1) {
            return $this->lookupStudent($persons[0]);
        }
    }

    /**
     * Get summary student information for the person specified person
     *
     */
    public function lookupStudent($person) {
        if (!$person || isset($person->errors)) {
            if ($GLOBALS['alluser-debug']) {
                echo "NO PERSON OR ERROR";
            }
            return false;
        }

        //logger->info("Looking up a student with person id %s"->format(personId))

        $newStudent = new student_info($person->id);
        $students = $this->ethosClient->getStudentByPersonId($newStudent->personId);

        if (!$students || (count($students) != 1)) {
            return false;
        }

        $student = $students[0];
        $studentAcademicPrograms = $this->ethosClient->getStudentAcademicProgramsByPersonId($newStudent->personId);

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

        if (isset($student->type) && $attendanceMode = $this->ethosClient->getStudentType($student->type->id)) {
            $newStudent->attendanceMode = $attendanceMode->code;
            $newStudent->attendanceModeTitle = $attendanceMode->title;
        }

        if (isset($student->status) && $statusObj = $this->ethosClient->getStudentStatus($student->status->id)) {
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

        if ($GLOBALS['alluser-debug']) $this->log(var_dump($newStudent));

        return $newStudent;
    }

    /**
     * Looks up the full academic program info for the StudentAcademicProgram
     */
    public function getProgramInfo($personId, $studentAcademicProgram) {

        $programInfo = new program_info();

        $academicProgram = $this->ethosClient->getAcademicProgram($studentAcademicProgram->program->id);
        $faculty = $this->ethosClient->getInstitution($academicProgram->authorizing->institutionalUnit->id);
        $academicProgramLevel = $this->ethosClient->getAcademicLevel($studentAcademicProgram->academicLevel->id);
        $period = $this->ethosClient->getAcademicPeriod($studentAcademicProgram->academicPeriods->starting->id);

        if ($studentAcademicProgram->site->id != null) {
            $site = $this->ethosClient->getSite($studentAcademicProgram->site->id);
            $programInfo->siteCode = $site->code;
            $programInfo->siteTitle = $site->title;
        }

        $programInfo->facultyCode = $faculty->code;
        $programInfo->facultyTitle = $faculty->title;
        $programInfo->schoolTypeCode = $academicProgramLevel->code;
        $programInfo->schoolTypeTitle = $academicProgramLevel->title;
        $programInfo->courseCode = $academicProgram->code;
        $programInfo->courseTitle = $academicProgram->title;
        $programInfo->preference = $studentAcademicProgram->preference;
        $programInfo->startOn = $this->ArrayToDateTime(date_parse($studentAcademicProgram->startOn)) ?? null;

        //TODO - were these blank in the Ethos client?
        //$programInfo->endOn = $studentAcademicProgram->endOn;
        //$programInfo->graduatedOn = $studentAcademicProgram->graduatedOn;
        //$programInfo->creditsEarned = $studentAcademicProgram->creditsEarned;
        $programInfo->enrollmentStatus = $studentAcademicProgram->enrollmentStatus->status;

        /** Dig out the period enrollment status stuff */
        $startingPeriodId = $studentAcademicProgram->academicPeriods->starting->id;
        $periodProfiles = $this->ethosClient->getAcademicPeriodProfiles($personId, $startingPeriodId);

        /** Dig out the student registration eligibility */
        // don't need this yet, but it works->
        //val eligibilities = ethosClient->getStudentRegistrationEligibility(personId, startingPeriodId)

        if (count($periodProfiles)) {
            $enrollmentStatus = $this->ethosClient->getEnrollmentStatus($periodProfiles[0]->academicPeriodEnrollmentStatus->id);
            $programInfo->periodProfileEnrollmentStatusCode = $enrollmentStatus->code;
            $programInfo->periodProfileEnrollmentStatusTitle = $enrollmentStatus->title;
        }

        $startOn = $this->ArrayToDateTime(date_parse($period->startOn));
        $endOn = $this->ArrayToDateTime(date_parse($period->endOn));

        $programInfo->periodInfo = new period_info($period->category->type, $period->category->parent->academicPeriod->id, $period->code, $period->title, $endOn, $period->id, $startOn, $period->registration);

                /*
        $programInfo->disciplines = $studentAcademicProgram->disciplines->stream()->map { d -> getDisciplineInfo(d) }?->collect(Collectors->toList())
        $programInfo->honours = $studentAcademicProgram->recognitions->stream()->map { r -> getHonoursInfo(r) }?->collect(Collectors->toList())
                */

        foreach ($studentAcademicProgram->disciplines as $discipline) {
            $programInfo->disciplines[] = $this->getDisciplineInfo($discipline->discipline->id);
        }


        $awardCredential = count($studentAcademicProgram->credentials) ? $studentAcademicProgram->credentials[0] : null;

        if ($awardCredential!=null) {
            $award = $this->ethosClient->getAcademicCredential($awardCredential->id);
            $programInfo->awardAbbreviation = $award->abbreviation;
            $programInfo->awardTitle = $award->title;
            $programInfo->awardType = $award->type;
        }

        return $programInfo;
    }


    public function getName($person) {
        $names = $person->names;

        // get official name
        $officialName = array_filter($names, function ($a) { return !isset($a->type); })[0];

        // Get the preferred / nickname
        $preferredNameArray = array_values(array_filter($names, function ($a) { return ((isset($a->type)) && ($a->type->category == "favored")); }));

        $name = new name_info();
        //$name->prefix = $officialName->title;
        $name->firstName = isset($officialName->firstName) ? $officialName->firstName : null;
        $name->lastName = isset($officialName->lastName) ? $officialName->lastName : null;
        $name->middleName = isset($officialName->middleName) ? $officialName->middleName : null;
        $name->fullName = isset($officialName->fullName) ? $officialName->fullName : null;
        $name->nickName = count($preferredNameArray) ? $preferredNameArray[0]->firstName : null;

        return $name;
    }


    public function getDisciplineInfo($disciplineId) {
        // TODO ethosDisciplineId.administeringInstitutionUnit should contain the department for the subject of study
        $discipline = $this->ethosClient->getAcademicDiscipline($disciplineId);
        return new discipline_info($discipline->abbreviation, $discipline->type, $discipline->title);
        
    }




    public function getStudentsWithChanges() {

        $messages = $this->ethosClient->consumeMessages();

        $count = count($messages);
        $this->log("Found $count messages");

        $studentArray = array();

        foreach ($messages as $message) {

            if (isset($message->resource) 
            && isset($message->content) 
            && isset($message->operation)
            && ($message->operation !== 'deleted')) {

                $resourceName = $message->resource->name;
                $messageContent = $message->content;
                
                switch ($resourceName) {
                    case 'persons':
                        $id = $messageContent->id;
                        break;
                    case 'students':
                        $id = $messageContent->person->id;
                        break;
                    case 'student-academic-period-profiles':
                        $id = $messageContent->person->id;
                        break;
                    case 'student-academic-programs':
                        $id = $messageContent->student->id;
                        break;
                }

                if (!in_array($id, $studentArray))
                {
                    $studentArray[] = $id; 
                }

            }

        }

        return $studentArray;

    }

}