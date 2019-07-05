<?php
namespace enrol_ethos\forms;

require_once("$CFG->libdir/formslib.php");
 
class process_user_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $DB, $CFG;





        
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}