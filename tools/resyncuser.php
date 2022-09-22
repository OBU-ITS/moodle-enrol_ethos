<?php
require_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once("$CFG->libdir/formslib.php");

admin_externalpage_setup('ethosenroltoolsholdresync');

$sitecontext = context_system::instance();
$site = get_site();

$PAGE->set_heading("Re-sync a user with Banner");
echo $OUTPUT->header();

class resyncuser_form extends moodleform {

    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('text', 'userid', get_string('tool_resyncuser_username_label', 'enrol_ethos'));
        $mform->setType('userid', PARAM_NOTAGS);
//        $mform->setDefault('userid', get_string('tool_resyncuser_username_defaultvalue', 'enrol_ethos'));
        $mform->addElement('submit', 'resyncbutton', get_string('tool_resyncuser_button_label', 'enrol_ethos'));
    }
    //Custom validation should be added here
    function validation($data, $files) {
        $errors = array();
        if (empty($data["userid"]) || $data["userid"] === get_string('tool_resyncuser_username_defaultvalue', 'enrol_ethos')){
            $errors[] = "error";
        }
        return $errors;
    }
}

$mform = new resyncuser_form();

if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    $sync = \enrol_ethos\services\sync\obu_sync_person_hold_service::getInstance();
    $trace = new \null_progress_trace();
    if($sync->reSyncUser($trace, $fromform->userid)) {
        $notification = "Form Submitted";
        \core\notification::info($notification);
    }
    else {
        $notification2 = "User could not be found, please try again";
        \core\notification::error($notification2);
    }

} else{

    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
    if ($mform->is_submitted() && !$mform->is_validated()){
        $notification2 = "User could not be found, please try again";
        \core\notification::error($notification2);
    }
}



$mform->display();

echo $OUTPUT->footer();