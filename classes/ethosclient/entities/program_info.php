<?php

namespace enrol_ethos\ethosclient\entities;

class program_info {

    public $guid;

    /** Typically the Banner Program of study */
    public $courseCode;
    public $courseTitle;

    /** College code in Banner */
    public $facultyCode;
    public $facultyTitle;

    /** school type FE, Foundation, Further Education, Postgraduate, Research, Undergraduate, University */
    public $schoolTypeCode;
    public $schoolTypeTitle;

    /* Academic level */
    public $academicLevelCode;
    public $academicLevelTitle;

    /**
     * Disciplines for the program of study
     *
     * @var discipline_info[]
     */
    public array $disciplines;

    /** The date on which an academic program begins **/
    public $startOn;

    /** The date on which an academic program ends */
    public $endOn;

    public $graduatedOn;

    /** The number of credits earned at the home institution during the course of study for the program **/
    public $creditsEarned;

    public period_info $periodInfo;

    /** Sets the preference of the course from Banner */
    public $preference;

    /** Enrollment status at program of study level */
    public $enrollmentStatus;

    /** Enrollment status of first period profile */
    public $periodProfileEnrollmentStatusCode;

    /** Enrollment status of first period profile */
    public $periodProfileEnrollmentStatusTitle;

    /** Campus code */
    public $siteCode;

    /** Campus title */
    public $siteTitle;

    /** The title of the award */
    public $awardTitle;

    /** The code for the award */
    public $awardAbbreviation;

    /** The type of award */
    public $awardType;

    /** List all of the honours granted to this academic program */
    public $honours;

    public function toString() : string {
        return "ProgramInfo(courseCode=$this->courseCode, courseTitle=$this->courseTitle, facultyCode=$this->facultyCode, facultyTitle=$this->facultyTitle, schoolTypeCode=$this->schoolTypeCode, schoolTypeTitle=$this->schoolTypeTitle, disciplines=$this->disciplines, startOn=$this->startOn, endOn=$this->endOn, graduatedOn=$this->graduatedOn, creditsEarned=$this->creditsEarned, periodInfo=$this->periodInfo, preference=$this->preference, enrollmentStatus=$this->enrollmentStatus, periodProfileEnrollmentStatusCode=$this->periodProfileEnrollmentStatusCode, periodProfileEnrollmentStatusTitle=$this->periodProfileEnrollmentStatusTitle, siteCode=$this->siteCode, siteTitle=$this->siteTitle, awardTitle=$this->awardTitle, awardAbbreviation=$this->awardAbbreviation, awardType=$this->awardType, honours=$this->honours)";
    }
}