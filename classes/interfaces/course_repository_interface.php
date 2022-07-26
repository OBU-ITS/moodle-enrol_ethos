<?php
namespace enrol_ethos\interfaces;
use enrol_ethos\entities\mdl_course;

interface course_repository_interface
{
    public function findOne($id);
    public function findOneByShortName($shortName);
    public function update(mdl_course $course);
    public function create(mdl_course $course);
    public function remove(mdl_course $course);
}