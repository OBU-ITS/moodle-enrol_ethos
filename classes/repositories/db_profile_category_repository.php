<?php
namespace enrol_ethos\repositories;
use enrol_ethos\interfaces\profile_category_repository_interface;
use enrol_ethos\entities\profile_category;

class db_profile_category_repository implements profile_category_repository_interface
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findOne($id)
    {
        // Find a record with the id = $id
        $category = $this->db->get_record('user_info_category',array('name'=>$id));

        if ($category) {
            return new profile_category($category->name, $category->sortorder, $category->id);
        }

        return null;
    }

    public function save(profile_category $profileCategory)
    {
        $cat = new \stdClass();
        $cat->name = $profileCategory->name;
        $cat->sortorder = $profileCategory->sortorder;
        return $this->db->insert_record('user_info_category', $cat);
    }

    public function remove(profile_category $profileCategory)
    {
        // Remove the $user
        // from the 'users' table
        //$this->db->remove($user, 'users');
    }
}
