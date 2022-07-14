<?php

namespace enrol_ethos\ethosclient\entities;

class student_info {

    /**
 * Defines the fields that make up a new student, summary info
 */
    /** The business key for the student */
    public $studentNumber;

    /** Basic BIO values */
    public $title;
    public $forename;
    public $surname;
    public $middleName;
    public $nickName;
    public $dateOfBirth;

    /** The banner Person ID GUID */
    public $personId;

    /** The banner Student ID GUID */
    public $studentId;

    /** Typically the Banner Program of study */
    public $courseCode;
    public $courseTitle;

    /** Oxford Brookes */
    public $establishmentCode;
    public $establishmentTitle;

    /** College code in Banner */
    public $facultyCode;
    public $facultyTitle;

    /** department in Banner, stvdept */
    public $departmentCode;
    public $departmentTitle;

    /** school type FE, Foundation, Further Education, Postgraduate, Research, Undergraduate, University */
    public $schoolTypeCode;
    public $schoolTypeTitle;


    public $attendanceMode;
    public $attendanceModeTitle;

    /**
     * This is the major code for the most relevant discipline
     */
    public $subjectCode;

    /** This is the title of the major for the most relevant discipline
     *
     */
    public $subjectTitle;

    /**
     * The code for the award BSC etc
     */
    public $awardCode;

    /**
     * The title of the award, bachelor of science
     */
    public $awardTitle;

    public $startDate;

    /** The end date of the academic programme **/
    public $endDate;

    /**
     * Active or Inactive
     *
     */
    public $status;

    public $statusTitle;

    /** The date the student graduate from the program **/
    public $graduatedOn;

    /** The list of academic recognitions the student has received**/
    public $recognitions;

    /** The number of credits earned at the home institution during the course of study for the program **/
    public $creditsEarned;

    /** The name of the academic level associated with the enrollment of the student in the academic program */
    public $academicLevel;

    /** List of all the academic programmes for the student */
    public $programmes;

    /** The primary active program of study for the student */
    public $leadProgramOfStudy;

    /** Does the person have the dyslexia services applied */
    public $dyslexic;

    public function toString() : string {
        return "StudentInfo(studentNumber=$this->studentNumber, title=$this->title, forename=$this->forename, surname=$this->surname, middleName=$this->middleName, nickName=$this->nickName, dateOfBirth=$this->dateOfBirth, personId=$this->personId, studentId=$this->studentId, courseCode=$this->courseCode, courseTitle=$this->courseTitle, establishmentCode=$this->establishmentCode, establishmentTitle=$this->establishmentTitle, facultyCode=$this->facultyCode, facultyTitle=$this->facultyTitle, departmentCode=$this->departmentCode, departmentTitle=$this->departmentTitle, schoolTypeCode=$this->schoolTypeCode, schoolTypeTitle=$this->schoolTypeTitle, attendanceMode=$this->attendanceMode, attendanceModeTitle=$this->attendanceModeTitle, subjectCode=$this->subjectCode, subjectTitle=$this->subjectTitle, awardCode=$this->awardCode, awardTitle=$this->awardTitle, startDate=$this->startDate, endDate=$this->endDate, status=$this->status, statusTitle=$this->statusTitle, graduatedOn=$this->graduatedOn, recognitions=$this->recognitions, creditsEarned=$this->creditsEarned, academicLevel=$this->academicLevel, programmes=$this->programmes, leadProgramOfStudy=$this->leadProgramOfStudy, dyslexic=$this->dyslexic)";
    }

    public function __construct($personId) {
        $this->personId = $personId;
    }

}