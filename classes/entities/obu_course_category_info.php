<?php
namespace enrol_ethos\entities;

class obu_course_category_info {
    public int $id;
    public string $name;
    public string $codeName;
    public string $alternateCodeName;

    public function __construct($name, $codeName, $alternateCodeName = '')
    {
        $this->id = -1;
        $this->name = $name;
        $this->codeName = $codeName;
        $this->alternateCodeName = $alternateCodeName;
    }
}