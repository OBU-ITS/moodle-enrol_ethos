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

        // TODO : Update profile field data

        try {
            update_course($moodleCourse);
        }
        catch (Exception $e) {
        }
    }

    public function create(mdl_course $course) : mdl_course
    {
        $moodleCourse = $this->convertToMoodleCourse($course);

        $course = create_course($moodleCourse);

        // TODO : create profile field data

        return $this->convertFromMoodleCourse($course);
    }

    private function convertFromMoodleCourse($dbCourse) : mdl_course {
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

    private function convertToMoodleCourse(mdl_course $course) : \stdClass {
        $moodleCourse = new \stdClass();
        $moodleCourse->id = $course->id;

        // Set some preferences.
        //$moodleCourse->format             = 'topics';
        //$moodleCourse->numsections        = 6;
        //$moodleCourse->hiddensections     = 0;
        $moodleCourse->newsitems            = 3;
        $moodleCourse->showgrades           = 1;
        $moodleCourse->showreports          = 1;

        $moodleCourse->idnumber = $course->idnumber;
        $moodleCourse->shortname = $course->shortname;
        $moodleCourse->fullname = $course->name;
        $moodleCourse->startdate = $course->startdate;
        $moodleCourse->enddate = $course->enddate;
        $moodleCourse->category = $course->catid;
        $moodleCourse->visible = $course->visible;

        return $moodleCourse;
    }
}
