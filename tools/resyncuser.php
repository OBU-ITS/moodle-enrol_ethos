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
        $mform->setDefault('userid', get_string('tool_resyncuser_username_defaultvalue', 'enrol_ethos'));
        $mform->addElement('submit', 'resyncbutton', get_string('tool_resyncuser_button_label', 'enrol_ethos'));
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}

$mform = new resyncuser_form();

if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    $notification = "Form Submitted";
    \core\notification::info($notification);
}
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.

    //displays the form

$mform->display();

echo $OUTPUT->footer();