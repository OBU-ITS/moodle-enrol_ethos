<?php
namespace enrol_ethos\repositories;

use enrol_ethos\entities\mdl_user;
use profile_field_base;
use stdClass;

require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

class db_user_repository extends \enrol_plugin
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get($id)
    {
        $sql  = 'select u.id AS userid, u.* ';
        $sql .= 'from {user} u ';
        $sql .=  'where u.id = :id';

        $dbuser = $this->db->get_record_sql($sql, ['id' => $id]);

        return $dbuser;
    }

    public function getByUsername($username) : ?mdl_user
    {
        $sql  = 'select u.id AS userid, u.* ';
        $sql .= 'from {user} u ';
        $sql .= 'where u.username = :username';

        return $this->convertToMoodleUser($this->db->get_record_sql($sql, ['username' => $username]));
    }

    public function getAllUsers($authType=null, $includeDeleted=true, int $limit = 0, int $offset = 0) {
        // Join any user info data present with each user info field for the user object.
        $sql = 'SELECT username, id AS userid ';
        $sql .= 'FROM {user} ';
        $sql .= 'where 1=1 ';

        if ($authType) {
            $sql .=  'and auth = :authtype ';
        }

        if (!$includeDeleted) {
            $sql .= 'and deleted = 0 ';
        }

        $sql .= 'ORDER BY username ';

        if($limit > 0) {
            $sql .= 'LIMIT :limit ';
        }
        if($offset > 0) {
            $sql .= 'OFFSET :offset ';
        }

        return $this->db->get_records_sql($sql, ['authtype' => $authType, 'limit' => $limit, 'offset' => $offset]);
    }

    public function getUsersByAuthType(string $authType, int $limit = 0, int $offset = 0) {
        return $this->getAllUsers($authType,false, $limit, $offset);
    }

    public function create(mdl_user $moodleUser) : mdl_user {
        $user = $this->convertFromMoodleUser($moodleUser);

        $user->modified   = time();
        $user->confirmed  = 1;
        $user->auth       = 'ldap';
        $user->suspended = 0;
        $user->mnethostid = 1;

        $dbUserId = user_create_user($user, false, false);
        $dbUser = $this->get($dbUserId);

        $newMoodleUser = $this->convertToMoodleUser($dbUser);

        $customData = $moodleUser
            ->getCustomData()
            ->getStandardClass($newMoodleUser->id);

        profile_save_data($customData);

        return $newMoodleUser;
    }

    public function update(mdl_user $moodleUser) {
        $dbUser = $this->convertFromMoodleUser($moodleUser);

        user_update_user($dbUser, false, true);

        $customData = $moodleUser
            ->getCustomData()
            ->getStandardClass($moodleUser->id);

        profile_save_data($customData);
    }

    private function convertFromMoodleUser(mdl_user $moodleUser) : stdClass {
        $user = new \stdClass();

        $user->id = $moodleUser->id;
        $user->username = trim(\core_text::strtolower($moodleUser->username));
        $user->firstname = $moodleUser->firstname;
        $user->lastname = $moodleUser->lastname;
        $user->email = $moodleUser->email;

        return $user;
    }

    private function convertToMoodleUser($dbUser) : ?mdl_user {
        if(!property_exists($dbUser, "userid")) {
            return null;
        }

        $moodleUser = new mdl_user();
        $moodleUser->id = $dbUser->userid;
        $moodleUser->username = $dbUser->username ?? '';
        $moodleUser->firstname = $dbUser->firstname ?? '';
        $moodleUser->lastname = $dbUser->lastname ?? '';
        $moodleUser->email = $dbUser->email ?? '';
        return $moodleUser;
    }

    public function save(user $user)
    {
        //Should we be marshalling between types here?

        $userProfileFields = array(
             'courseCode' => 0,
             'courseTitle' => 0,
             'establishmentCode' => 0,
             'establishmentTitle' => 0,
             'facultyCode' => 0,
             'facultyTitle' => 0,
             'departmentCode' => 0,
             'departmentTitle' => 0,
             'schoolTypeCode' => 0,
             'schoolTypeTitle' => 0,
             'attendanceMode' => 0,
             'attendanceModeTitle' => 0,
             'subjectCode' => 0,
             'subjectTitle' => 0,
             'awardCode' => 0,
             'awardTitle' => 0,
             'startDate' => 0,
             'endDate' => 0,
             'status' => 0,
             'statusTitle' => 0,
             'graduatedOn' => 0,
             'recognitions' => 0,
             'creditsEarned' => 0,
             'academicLevel' => 0,
             'dyslexic' => 0,
             'bannerGuid' => 0);
             /*'programmes' => 0,
             'leadProgramOfStudy' => 0);*/

        $fields = $this->db->get_records('user_info_field');

        #TODO - use select menu / array merge?
        foreach ($fields as $key => $value) {
            if (isset($userProfileFields[$value->shortname])) {
                $userProfileFields[$value->shortname] = $key;
            }
        }

        $id = $user->id;
        $userProfile = $user->userProfile;

        # Get all the current data records
        $fieldDataRecords = $this->db->get_records_menu('user_info_data', array('userid'=>$id), '', 'fieldid,id');

        $newRecords = array();

        foreach ($userProfileFields as $shortname => $fieldid) {

            $data = new \stdClass();

            $data->userid  = $user->id;
            $data->fieldid = $fieldid;

            $content = $userProfile->{$shortname};

            if ($content instanceof \DateTime) {
                $content = $content->getTimestamp();
            }

            $data->data = $content === null ? '' : $content;

            #If already a field with this field id
            if (isset($fieldDataRecords[$fieldid])) {
                $data->id = $fieldDataRecords[$fieldid];
                $this->db->update_record('user_info_data', $data);
            } else {
                array_push($newRecords, $data);
            }
        }

        if (count($newRecords)) {
            $this->db->insert_records('user_info_data', $newRecords);
        }

        //$this->unenrol_missing_enrolments($user);

        foreach ($user->enrolments as $enrolment) {
            $restrictStart = 0;
            $restrictEnd = 0;

            $roleId = 5; // This might not be correct everywhere...

            $this->assign_role( $roleId,
                                $enrolment->course->id,
                                $user->id,
                                $restrictStart,
                                $restrictEnd);
        }
    }

    /**
     * @param int $id User Id
     * @return array
     */
    public function getUserProfileData(int $id) : array {
        $customDataRaw = array();

        $fields = profile_get_user_fields_with_data($id);
        foreach ($fields as $formField) {
            $customDataRaw[$formField->field->shortname] = $formField->data;
        }

        return $customDataRaw;
    }

    public function  getUserWhereProfileFieldContains(string $profileFieldShortName, string $profileFieldValue) : ?mdl_user {
        $condition = $this->db->sql_like('uind.data', ':value');
        $value = '%'.$this->db->sql_like_escape($profileFieldValue).'%';
        return $this->userWhereProfileField($profileFieldShortName, $condition, $value);
    }

    public function getUserWhereProfileFieldEquals(string $profileFieldShortName, string $profileFieldValue) : ?mdl_user {
        return $this->userWhereProfileField($profileFieldShortName, "uind.data = :value", $profileFieldValue);
    }

    private function userWhereProfileField(string $shortname, string $condition, string $value) : ?mdl_user {
        $sql  = 'select u.id AS userid, u.*, uind.id AS hasuserdata ';
        $sql .= 'from {user} u ';
        $sql .= 'join {user_info_data} uind on uind.userid = u.id ';
        $sql .= 'join {user_info_field} uif on uind.fieldid = uif.id ';
        $sql .=  'where uif.shortname = :shortname ';
        $sql .=  'and ' . $condition;

        $record = $this->db->get_record_sql($sql, ['shortname' => $shortname, 'value' => $value]);

        return $this->convertToMoodleUser($record);
    }

    /**
     * @param string $profileFieldShortName
     * @param string|null $profileFieldValue
     * @param string|null $authType
     * @return mdl_user[]
     */
    public function getAllUsersWithProfileFieldData(string $profileFieldShortName, string $profileFieldValue = null, string $authType = null) : array {
        $sql  = 'select u.id AS userid, u.*, uind.id AS hasuserdata ';
        $sql .= 'from {user} u ';
        $sql .= 'join {user_info_data} uind on uind.userid = u.id ';
        $sql .= 'join {user_info_field} uif on uind.fieldid = uif.id ';
        $sql .=  'where uif.shortname = :shortname';
        if ($profileFieldValue) {
            $sql .= ' and uind.data = :value';
        }
        else {
            $sql .= ' and uind.data is not null and uind.data <> \'\'';
        }
        if ($authType) {
            $sql .= ' and u.auth = :authtype';
        }

        $dbUsers = $this->db->get_records_sql($sql, ['shortname' => $profileFieldShortName, 'value' => $profileFieldValue, 'authtype' => $authType]);

        return array_filter(array_map(array($this, 'convertToMoodleUser'), $dbUsers), function ($item) { return $item != null; });
    }

    public function getUsersWithoutProfileFieldData(string $profileFieldShortName, string $authType = null) {
        $sql  = 'select u.id AS userid, u.* ';
        $sql .= 'from {user} u ';
        $sql .= 'join {user_info_data} uind on uind.userid = u.id ';
        $sql .= 'join {user_info_field} uif on uind.fieldid = uif.id ';
        $sql .= 'where uif.shortname = :shortname ';
        $sql .= 'and (uind.id is null or uind.data is null or uind.data = \'\')';
        if ($authType) {
            $sql .=  'and u.auth = :authtype ';
        }

        $dbUsers = $this->db->get_records_sql($sql, ['shortname' => $profileFieldShortName, 'authtype' => $authType]);

        return array_map(array($this, 'convertToMoodleUser'), $dbUsers);
    }

    public function getUsersByProfileField(string $profileFieldShortName, array $dataArray) {
        // Join any user info data present with each user info field for the user object.

        /*
        $sql = 'SELECT uif.*, uic.name AS categoryname ';
        $sql .= ', uind.id AS hasuserdata, uind.userid, uind.data, uind.dataformat ';

        $sql .= 'FROM {user_info_field} uif ';
        $sql .= 'LEFT JOIN {user_info_category} uic ON uif.categoryid = uic.id ';
        $sql .= 'LEFT JOIN {user_info_data} uind ON uif.id = uind.fieldid ';
        $sql .= 'WHERE uif.shortname = :shortname ';
        $sql .= 'ORDER BY uic.sortorder ASC, uif.sortorder ASC ';
        */

        //Max 100 users per query

        $dataArrayCount = count($dataArray);

        $dbusers = array();

        for ($i=0; $i<$dataArrayCount+99;$i+=100) {
            $n = $i < ($dataArrayCount-1) ? $i : ($dataArrayCount-1);

            $slice = array_slice($dataArray, $n, 100);

            $count = count($slice);
            $field = 'data';

            if ($count == 1) {
                $select = "$field = ?";
            } else {
                $qs = str_repeat(',?', $count);
                $qs = ltrim($qs, ',');
                $select = "$field IN ($qs)";
            }

            $sql  = 'select u.id AS userid, username, data, uind.id AS hasuserdata ';
            $sql .= 'from {user} u ';
            $sql .= 'join {user_info_data} uind on uind.userid = u.id ';
            $sql .= 'join {user_info_field} uif on uind.fieldid = uif.id ';
            //$sql .=  'where uif.shortname = :shortname ' ;
            $sql .=  'where uif.shortname = ? ' ;
            $sql .= 'and ' . $select;

            $bindValues = $slice;
            array_unshift($bindValues, $profileFieldShortName);

            $dbusersBatch = $this->db->get_records_sql($sql, $bindValues);
            $dbusers = array_merge($dbusersBatch, $dbusers);
        }

        return array_map(array($this, 'convertToMoodleUser'), $dbusers);
    }

    /**
     * Assigns a moodle role to a user in the provided course
     *
     * @param int $roleid id of the moodle role to assign
     * @param int $courseid id of the course to assign
     * @param int $userid id of the moodle user
     * @param string $logline passed logline object to append log entries to
     * @param int $restrictstart Start date of the enrolment
     * @param int $restrictend End date of the enrolment
     * @return bool success or failure of the role assignment
     */
    private function assign_role($roleid, $courseid, $userid, $restrictstart = 0, $restrictend = 0) {

        global $CFG;

        require_once($CFG->dirroot . '/enrol/ethos/lib.php');

        if (!enrol_is_enabled('ethos')) {
            return;
        }

        // Instance of enrol_ethos_plugin.
        $plugin = enrol_get_plugin('ethos');
        $result = $plugin->assign_role($roleid, $courseid, $userid, $restrictstart, $restrictend);
        return $result;
    }

//    private function unenrol_missing_enrolments($user) {
//        $courseIds = array_column(array_column($user->enrolments, 'course'), 'id');
//
//        $params = array('now'=>time(), 'userid'=>$user->id);
//
//        $sql = "SELECT ue.*, e.courseid as courseid, c.id AS contextid
//                    FROM {user_enrolments} ue
//                    JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'ethos')
//                    JOIN {context} c ON (c.instanceid = e.courseid AND c.contextlevel = 50)
//                    WHERE ue.userid = :userid";
//        $rs = $this->db->get_recordset_sql($sql, $params);
//
//        foreach ($rs as $ue) {
//            if (!in_array($ue->courseid, $courseIds)) {
//                $this->unassign_role(5, $ue->courseid, $user->id);
//            }
//        }
//        $rs->close();
//    }

    private function unassign_role($roleid, $courseid, $userid) {
        global $CFG;

        require_once($CFG->dirroot . '/enrol/ethos/lib.php');

        if (!enrol_is_enabled('ethos')) {
            return;
        }

        // Instance of enrol_ethos_plugin.
        $plugin = enrol_get_plugin('ethos');
        $result = $plugin->unassign_role($roleid, $courseid, $userid);
        return $result;
    }

}