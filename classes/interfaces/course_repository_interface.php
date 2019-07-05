<?php
namespace enrol_ethos\interfaces;
use enrol_ethos\entities\course;

interface course_repository_interface
{
    public function findOne($id);
    public function findOneByShortName($shortName);
    public function update(course $course);
    public function create(course $course);
    public function remove(course $course);
}