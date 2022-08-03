<?php
namespace enrol_ethos\entities;

use enrol_ethos\ethosclient\entities\ethos_educational_institution_unit_info;
use enrol_ethos\ethosclient\entities\ethos_site_info;
use enrol_ethos\ethosclient\entities\ethos_subject_info;

class obu_course_categories_info {

    private const LOCAL_CAMPUS_CODES = array('OBO','OBS','DL', 'IPC');

    /**
     * @var obu_course_category_info[]
     */
    private array $categories = array();

    /**
     * @return array
     */
    public function getCategories() : array {
        return $this->categories;
    }

    public function __construct(
        ethos_site_info $campus,
        ethos_educational_institution_unit_info $college,
        ?ethos_educational_institution_unit_info $department,
        ?ethos_subject_info $subject)
    {
        if(in_array($campus->code, $this::LOCAL_CAMPUS_CODES)) {
            $this->categories[] = new obu_course_category_info("SRS-Linked", "SRS", "SRS~~");
            $this->categories[] = new obu_course_category_info($college->title, $college->code);
            if(isset($subject)) {
                $this->categories[] = new obu_course_category_info($department->title, $department->code);
                $this->categories[] = new obu_course_category_info($subject->title, $subject->abbreviation);
            }
        }
        else {
            $this->categories[] = new obu_course_category_info("SRS-Linked", "SRS");
            $this->categories[] = new obu_course_category_info("Associated Colleges", "Assoc");
            $this->categories[] = new obu_course_category_info($campus->title, $campus->code);
            if(isset($subject)) {
                $this->categories[] = new obu_course_category_info($college->title, $college->code, $college->code . '~');
                $this->categories[] = new obu_course_category_info($subject->title, $subject->abbreviation);
            }
            else{
                $this->categories[] = new obu_course_category_info($college->title, $college->code);
            }
        }

    }
}