<?php
namespace enrol_ethos\interfaces;
use enrol_ethos\entities\profile_field;

interface profile_field_repository_interface
{
    public function findOne($id);
    public function save(profile_field $profileField);
    public function remove(profile_field $profileField);
}