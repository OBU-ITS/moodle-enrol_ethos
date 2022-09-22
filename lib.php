<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/user/profile/lib.php');

class enrol_ethos_plugin extends enrol_plugin {

    public function assign_role($roleid, $courseid, $userid, $restrictstart = 0, $restrictend = 0) {
        if ($instance = $this->get_instance($courseid)) {

            /*
            if ($this->get_config('recovergrades')) {
                $wasenrolled = is_enrolled(context_course::instance($courseid), $userid);
            }
            // TODO catch exceptions thrown.
            if ($this->get_config('recovergrades') && !$wasenrolled) {
                $logline .= 'recovering grades:';
                $recover = true;
            } else {
                $recover = false;
            }
            */

            $recover = false;

            /*

            if ($this->get_config('userestrictdates')) {
                if ((($restrictstart === 0) && ($restrictend === 0)) || (($restrictstart < time()) && (($restrictend === 0) || (time() < $restrictend)))) {
                    $userstatus = ENROL_USER_ACTIVE;
                } else {
                    $userstatus = ENROL_USER_SUSPENDED;
                }
            } else {
                $userstatus = ENROL_USER_ACTIVE;
                $restrictstart = 0;
                $restrictend = 0;
            }

            */

            $userstatus = ENROL_USER_ACTIVE;
            $restrictstart = 0;
            $restrictend = 0;

            $this->enrol_user($instance, $userid, $roleid, $restrictstart, $restrictend, $userstatus, $recover);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Unassigns a moodle role to a user in the provided course
     *
     * @param int $roleid id of the moodle role to unassign
     * @param int $courseid id of the course to unassign
     * @param int $userid id of the moodle user
     * @param string $logline passed logline object to append log entries to
     * @return bool success or failure of the role assignment
     */
    public function unassign_role($roleid, $courseid, $userid) {
        if (!$courseid) {
            return false;
        }

        if ($instance = $this->get_instance($courseid)) {
            // TODO catch exceptions thrown.
            $this->unenrol_user($instance, $userid, $roleid);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns name of this enrol plugin
     * @return string
     */
    public function get_name() : string {
        return "banner";
    }

     /**
     * Returns enrolment instance in given course.
     * @param int $courseid
     * @return object of enrol instances, or false
     */
    public function get_instance($courseid) {
        global $DB;
        $instance = $DB->get_record('enrol', array('courseid' => $courseid, 'enrol' => 'ethos'));
        // TODO add option to disable this.
        if (!$instance) {
            if ($course = $DB->get_record('course', array('id' => $courseid))) {
                $this->add_instance($course);
                $instance = $DB->get_record('enrol', array('courseid' => $courseid, 'enrol' => 'ethos'));
            }
        }
        return $instance;
    }

    /**
     * Return true if we can add a new instance to this course.
     *
     * @param int $courseid
     * @return boolean
     */
    public function can_add_instance($courseid) {
        return true;
    }

    /**
     * Add new instance of enrol plugin with default settings.
     * @param stdClass $course
     * @return int id of new instance, null if can not be created
     */
    public function add_default_instance($course) {
        $expirynotify = $this->get_config('expirynotify', 0);
        if ($expirynotify == 2) {
            $expirynotify = 1;
            $notifyall = 1;
        } else {
            $notifyall = 0;
        }
        $fields = array(
            'status'          => $this->get_config('status'),
            'roleid'          => $this->get_config('roleid', 0),
            'enrolperiod'     => $this->get_config('enrolperiod', 0),
            'expirynotify'    => $expirynotify,
            'notifyall'       => $notifyall,
            'expirythreshold' => $this->get_config('expirythreshold', 86400),
        );
        return $this->add_instance($course, $fields);
    }

    /**
     * Add new instance of enrol plugin.
     * @param stdClass $course
     * @param array|null instance fields
     * @return int|null id of new instance, null if can not be created
     */
    public function add_instance(stdClass $course, ?array $fields = null) : ?int {
        global $DB;

        if ($DB->record_exists('enrol', array('courseid'=>$course->id, 'enrol'=>$this->get_name()))) {
            return null;
        }

        return parent::add_instance($course, $fields);
    }

    /**
     * Update instance of enrol plugin.
     * @param stdClass $instance
     * @param stdClass $data modified instance fields
     * @return boolean
     */
    public function update_instance(stdClass $instance, stdClass $data) : bool {
        global $DB;

        if ($instances = $DB->get_records('enrol', array('courseid' => $instance->courseid, 'enrol' => $this->get_name()), 'id ASC')) {
            foreach ($instances as $anotherInstance) {
                if ($anotherInstance->id != $instance->id) {
                    parent::delete_instance($anotherInstance);
                }
            }
        }
        return parent::update_instance($instance, $data);
    }

}


function enrol_ethos_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course){

    $category = new core_user\output\myprofile\category('profilefieldscat',get_string('profilefields', 'enrol_ethos') , null);
    $tree->add_category($category);
    $profileFields = profile_get_user_fields_with_data($user->id);
    global $USER;
    $userType = $USER->profile["user_type"];

    foreach ($profileFields as $field){
        $shortname = $field->field->shortname;
        $data = $field->data;
        $name = $field->field->name;
        $fieldcatname = $field->get_category_name();

        if (strpos($fieldcatname, 'Hidden') !== false || empty($data)){
            continue;
        }

        if ($shortname === "student_completion_date"){
            $data = date('d-m-Y', $data);
        }

        if (strcasecmp($userType, "STAFF") == 0){
            $node = new core_user\output\myprofile\node('profilefieldscat', $shortname, $name, null, null, $data);
            $tree->add_node($node);
        }

        elseif(strcasecmp($userType, "STUDENT") == 0){
            if (strpos($fieldcatname, 'Student') !== false){
                $node = new core_user\output\myprofile\node('profilefieldscat', $shortname, $name, null, null, $data);
                $tree->add_node($node);
            }
        }
    }
}