<?php
namespace enrol_ethos\interfaces;

interface course_category_repository_interface
{
    public function findOne($id);
    public function findOneByNameAndParent($name, $parent);
}