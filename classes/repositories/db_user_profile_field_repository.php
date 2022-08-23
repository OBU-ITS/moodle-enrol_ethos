<?php
namespace enrol_ethos\repositories;

use enrol_ethos\entities\mdl_profile_field;

class db_user_profile_field_repository
{
    protected $db;

    private function __construct()
    {
        global $DB;

        $this->db = $DB;
    }

    private static ?db_user_profile_field_repository $instance = null;
    public static function getInstance() : db_user_profile_field_repository
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(int $id) : ?mdl_profile_field
    {
        if ($item = $this->db->get_record('user_info_field', array('shortname' => $id))) {
            return $this->convert($item);
        }

        return null;
    }

    /**
     * @param int $id
     * @return mdl_profile_field[]
     */
    public function getByCategoryId(int $id) : array {
        $sql = 'SELECT uif.* ';
        $sql .= 'FROM {user_info_field} uif ';
        $sql .= 'WHERE uif.categoryid = :categoryid';

        $items = $this->db->get_records_sql($sql, ['categoryid' => $id]);

        return array_map(array($this, 'convert'), $items);
    }

    private function convert($item) : mdl_profile_field {
        $obj = new mdl_profile_field();
        $obj->populateObject($item);
        return $obj;
    }

    public function save(mdl_profile_field $profileField)
    {
        if (!$profileField->id) {
            if ($id = $this->db->insert_record('user_info_field', $profileField)) {
                $profileField->id = $id;
                $result = true;
            }
            else{
                $result = false;
            }
        } else {
            $result = $this->db->update_record('user_info_field', $profileField);
        }

        return $result ? $profileField : false;
    }

    public function remove(mdl_profile_field $profileField)
    {
        // Remove the $user
        // from the 'users' table
        //$this->db->remove($user, 'users');
    }
}
