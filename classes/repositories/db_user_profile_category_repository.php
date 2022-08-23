<?php
namespace enrol_ethos\repositories;

use enrol_ethos\entities\mdl_profile_category;

class db_user_profile_category_repository
{
    protected $db;

    private function __construct()
    {
        global $DB;

        $this->db = $DB;
    }

    private static ?db_user_profile_category_repository $instance = null;
    public static function getInstance() : db_user_profile_category_repository
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?mdl_profile_category
    {
        if ($item = $this->db->get_record('user_info_category',array('id'=>$id))) {
            return $this->convert($item);
        }

        return null;
    }

    public function getByName($name) : ?mdl_profile_category
    {
        if ($item = $this->db->get_record('user_info_category',array('name'=>$name))) {
            return $this->convert($item);
        }

        return null;
    }

    private function convert($item) : mdl_profile_category {
        $obj = new mdl_profile_category();
        $obj->populateObject($item);
        return $obj;
    }

    public function create(string $name) : mdl_profile_category {
        $cat = new \stdClass();
        $cat->name = $name;
        $cat->sortorder = (int)$this->db->get_field_sql('SELECT MAX(sortorder) FROM {user_info_category}') + 1;

        $id = $this->db->insert_record('user_info_category', $cat);

        return $this->get($id);
    }

    public function remove(mdl_profile_category $profileCategory)
    {
        // Remove the $user
        // from the 'users' table
        //$this->db->remove($user, 'users');
    }
}
