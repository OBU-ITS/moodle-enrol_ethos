<?php
namespace enrol_ethos\interfaces;
use enrol_ethos\entities\profile_category;

interface profile_category_repository_interface
{
    public function findOne($id);
    public function save(profile_category $profileCategory);
    public function remove(profile_category $profileCategory);
}