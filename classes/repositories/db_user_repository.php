<?php
namespace enrol_ethos\repositories;
use enrol_ethos\interfaces\user_repository_interface;
use enrol_ethos\entities\user;

require_once($CFG->dirroot.'/lib/enrollib.php');

class db_user_repository extends \enrol_plugin implements user_repository_interface
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findOne($id)
    {
        $sql  = 'select u.id AS userid, username ';
        $sql .= 'from {user} u ';
        $sql .=  'where u.id = :id';    
        
        $dbuser = $this->db->get_record_sql($sql, ['id' => $id]);

        return $dbuser;    
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
             'dyslexic' => 0);
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

    public function remove(user $user)
    {
        // Remove the $user
        // from the 'users' table
        //$this->db->remove($user, 'users');
    }

    public function getAllUsersWithProfileFieldData(string $profileFieldShortName) {
        // Join any user info data present with each user info field for the user object.
    
        $sql  = 'select u.id AS userid, username, data, uind.id AS hasuserdata ';
        $sql .= 'from {user} u ';
        $sql .= 'join {user_info_data} uind on uind.userid = u.id ';
        $sql .= 'join {user_info_field} uif on uind.fieldid = uif.id '; 
        $sql .=  'where uif.shortname = :shortname';    
        
        $dbusers = $this->db->get_records_sql($sql, ['shortname' => $profileFieldShortName]);

        return $dbusers;
    }

    public function getUsersWithoutProfileFieldData(string $profileFieldShortName) {
        $sql  = 'select u.id AS userid, username ';
        $sql .= 'from {user} u ';
        $sql .= 'join {user_info_data} uind on uind.userid = u.id ';
        $sql .= 'join {user_info_field} uif on uind.fieldid = uif.id '; 
        $sql .= 'where uif.shortname = :shortname ';
        $sql .= 'and (uind.id is null or uind.data is null or uind.data = \'\')';

        $dbusers = $this->db->get_records_sql($sql, ['shortname' => $profileFieldShortName]);

        return $dbusers;
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

        return $dbusers;
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


    public function getAllUsers() {
        // Join any user info data present with each user info field for the user object.
        $sql = 'SELECT username, id AS userid ';
        $sql .= 'FROM {user} ';
        $sql .= 'ORDER BY username ';

        $dbusers = $this->db->get_records_sql($sql);

        return $dbusers;
    }
}