<?php
namespace enrol_ethos\repositories;
use enrol_ethos\interfaces\profile_field_repository_interface;
use enrol_ethos\entities\profile_field;

class db_profile_field_repository implements profile_field_repository_interface
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findOne($id)
    {
        // Find a record with the id = $id
        // from the 'users' table
        // and return it as a User object
        //return $this->db->find($id, 'users', 'User');
        $field = $this->db->get_record('user_info_field', array('shortname' => $id));

        if ($field) {
            return new profile_field($field->shortname, 
                                     $field->name, 
                                     $field->description,
                                     $field->categoryid, 
                                     $field->datatype,
                                     $field->required,
                                     $field->locked,
                                     $field->forceunique,
                                     $field->signup,
                                     $field->visible);
        }

        return null;
    }

    public function save(profile_field $profileField)
    {
        //Should we be marshalling between types here?
        return $this->db->insert_record('user_info_field', $profileField);
    }

    public function remove(profile_field $user)
    {
        // Remove the $user
        // from the 'users' table
        //$this->db->remove($user, 'users');
    }
}
