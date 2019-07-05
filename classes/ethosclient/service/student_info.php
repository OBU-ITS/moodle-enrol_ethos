<?php

namespace enrol_ethos\ethosclient\service;

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

    /** Does the person have the dyslexia service applied */
    public $dyslexic;

    public function toString() {
        return "StudentInfo(studentNumber=$studentNumber, title=$title, forename=$forename, surname=$surname, middleName=$middleName, nickName=$nickName, dateOfBirth=$dateOfBirth, personId=$personId, studentId=$studentId, courseCode=$courseCode, courseTitle=$courseTitle, establishmentCode=$establishmentCode, establishmentTitle=$establishmentTitle, facultyCode=$facultyCode, facultyTitle=$facultyTitle, departmentCode=$departmentCode, departmentTitle=$departmentTitle, schoolTypeCode=$schoolTypeCode, schoolTypeTitle=$schoolTypeTitle, attendanceMode=$attendanceMode, attendanceModeTitle=$attendanceModeTitle, subjectCode=$subjectCode, subjectTitle=$subjectTitle, awardCode=$awardCode, awardTitle=$awardTitle, startDate=$startDate, endDate=$endDate, status=$status, statusTitle=$statusTitle, graduatedOn=$graduatedOn, recognitions=$recognitions, creditsEarned=$creditsEarned, academicLevel=$academicLevel, programmes=$programmes, leadProgramOfStudy=$leadProgramOfStudy, dyslexic=$dyslexic)";
    }

    public function __construct($personId) {
        $this->personId = $personId;
    }

}