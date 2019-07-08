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

/**
 * Flatfile enrolment plugin.
 *
 * This plugin lets the user specify a "flatfile" (CSV) containing enrolment information.
 * On a regular cron cycle, the specified file is parsed and then deleted.
 *
 * @package    enrol_ethos
 * @copyright  2010 Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Flatfile enrolment plugin implementation.
 *
 * Comma separated file assumed to have four or six fields per line:
 *   operation, role, idnumber(user), idnumber(course) [, starttime [, endtime]]
 * where:
 *   operation        = add | del
 *   role             = student | teacher | teacheredit
 *   idnumber(user)   = idnumber in the user table NB not id
 *   idnumber(course) = idnumber in the course table NB not id
 *   starttime        = start time (in seconds since epoch) - optional
 *   endtime          = end time (in seconds since epoch) - optional
 *
 * @author  Eugene Venter - based on code by Petr Skoda, Martin Dougiamas, Martin Langhoff and others
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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

}