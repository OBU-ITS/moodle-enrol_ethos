<?php

namespace enrol_ethos\ethosclient\service;

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

    /** Disciplines for the program of study */
    public $disciplines;

    /** The date on which an academic program begins **/
    public $startOn;

    /** The date on which an academic program ends */
    public $endOn;

    public $graduatedOn;

    /** The number of credits earned at the home institution during the course of study for the program **/
    public $creditsEarned;

    public $periodInfo;

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
    
    public function toString() {
        return "ProgramInfo(courseCode=$courseCode, courseTitle=$courseTitle, facultyCode=$facultyCode, facultyTitle=$facultyTitle, schoolTypeCode=$schoolTypeCode, schoolTypeTitle=$schoolTypeTitle, disciplines=$disciplines, startOn=$startOn, endOn=$endOn, graduatedOn=$graduatedOn, creditsEarned=$creditsEarned, periodInfo=$periodInfo, preference=$preference, enrollmentStatus=$enrollmentStatus, periodProfileEnrollmentStatusCode=$periodProfileEnrollmentStatusCode, periodProfileEnrollmentStatusTitle=$periodProfileEnrollmentStatusTitle, siteCode=$siteCode, siteTitle=$siteTitle, awardTitle=$awardTitle, awardAbbreviation=$awardAbbreviation, awardType=$awardType, honours=$honours)";
    }
} 