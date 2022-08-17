<?php
namespace enrol_ethos\repositories;
use enrol_ethos\entities\mdl_course;

require_once($CFG->dirroot.'/course/lib.php');

class db_course_repository
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

    public function getAllCoursesWithCustomFieldData(string $profileFieldShortName, string $profileFieldValue = null) {
        $sql  = 'select c.* ';
        $sql .= 'from {course} c ';
        $sql .= 'join {customfield_data} cd on c.id = cd.instanceid ';
        $sql .= 'join {customfield_field} cf on cd.fieldid = cf.id ';
        $sql .= 'join {customfield_category} cc on cf.categoryid = cc.id ';
        $sql .=  'where cc.component = \'core_course\' AND cc.area = \'course\' AND cf.shortname = :shortname';
        if ($profileFieldValue) {
            $sql .=  ' AND cd.value = :value';
        }

        return $this->db->get_records_sql($sql, ['shortname' => $profileFieldShortName, 'value' => $profileFieldValue]);
    }

    public function update(mdl_course $course) {
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

    public function create(mdl_course $course)
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
        $course = new mdl_course(
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

    private function convertToMoodleCourse(mdl_course $course) {
        $moodlecourse = new \stdClass();

        if (empty($course->enddate)) {
            $enddate = 0;
        }
        if (empty($course->startdate)) {
            $startdate = 0;
        }
        // Set some preferences.
        $logline = 'Using hard-coded settings:';
        //$moodlecourse->format               = 'topics';
        //$moodlecourse->numsections          = 6;
        //$moodlecourse->hiddensections       = 0;
        $moodlecourse->newsitems            = 3;
        $moodlecourse->showgrades           = 1;
        $moodlecourse->showreports          = 1;

        $moodlecourse->idnumber = $course->idnumber;
        $moodlecourse->shortname = $course->shortname;
        $moodlecourse->fullname = $course->name;
        $moodlecourse->startdate = $course->startdate;
        $moodlecourse->enddate = $course->enddate;
        $moodlecourse->category = $course->catid;
        $moodlecourse->visible = $course->visible;

        return $moodlecourse;
    }
}
