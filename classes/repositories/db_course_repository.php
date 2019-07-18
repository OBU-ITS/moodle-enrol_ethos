<?php
namespace enrol_ethos\repositories;
use enrol_ethos\entities\course as course;

require_once($CFG->dirroot.'/course/lib.php');

class db_course_repository implements \enrol_ethos\interfaces\course_repository_interface
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findOne($idNumber) {
        if ($moodleCourse = $this->db->get_record('course', array('idnumber' => $idNumber))) {
            return $this->convertFromMoodleCourse($moodleCourse);
        } else {
            return false;
        }
    }

    public function findOneByShortName($shortName) {
        if ($moodleCourse = $this->db->get_record('course', array('shortname' => $shortName))) {
            return $this->convertFromMoodleCourse($moodleCourse);
        } else {
            return false;
        }
    }


    public function update(course $course) {
        $moodleCourse = $this->convertToMoodleCourse($course);

        $moodleCourse->timemodified = time();
        $moodleCourse->id = $course->id;

        try {
            update_course($moodleCourse);
        }
        catch (Exception $e) {
            $status = false;
            return false;
        }
        return true;
    }

    public function create(course $course)
    {        
        $status = true;
        $logline = '';
        $moodleCourse = $this->convertToMoodleCourse($course);

        $timecreated = time();
        $moodleCourse->timecreated = $timecreated;
        $moodleCourse->timemodified = $timecreated;

        return $this->convertFromMoodleCourse(create_course($moodleCourse));
    }

    private function convertFromMoodleCourse($dbCourse) {
        $course = new course( 
            $dbCourse->idnumber, 
            $dbCourse->shortname, 
            $dbCourse->fullname, 
            $dbCourse->category, 
            $dbCourse->id,
            $dbCourse->startdate, 
            $dbCourse->enddate,
            false, //meta - todo
            $dbCourse->visible
        );

        return $course;
    }

    private function convertToMoodleCourse(course $course) {
        $moodlecourse = new \stdClass();

        if (empty($course->enddate)) {
            $enddate = 0;
        }
        if (empty($course->startdate)) {
            $startdate = 0;
        }
        // Set some preferences.
        $logline = 'Using hard-coded settings:';
        $moodlecourse->format               = 'topics';
        $moodlecourse->numsections          = 6;
        $moodlecourse->hiddensections       = 0;
        $moodlecourse->newsitems            = 3;
        $moodlecourse->showgrades           = 1;
        $moodlecourse->showreports          = 1;
    
        $moodlecourse->idnumber = $course->idnumber;
        //$moodleCourse->id = $course->id;
        $moodlecourse->shortname = $course->shortname;
        $moodlecourse->fullname = $course->name;
        $moodlecourse->startdate = $course->startdate;
        $moodlecourse->enddate = $course->enddate;
        $moodlecourse->category = $course->catid;
        $moodlecourse->visible = $course->visible;

        return $moodlecourse;
    }

    public function remove(course $course)
    {
        // Remove the $user
        // from the 'users' table
        //$this->db->remove($user, 'users');
    }

}
