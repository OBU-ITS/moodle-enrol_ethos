<?php
//
//    require_once('../../../config.php');
//    require_once($CFG->libdir.'/adminlib.php');
//    require_once($CFG->libdir.'/authlib.php');
//    require_once($CFG->dirroot.'/user/lib.php');
//
//    $createpsuedocourses  = optional_param('createpsuedocourses', 0, PARAM_INT);
//
//    admin_externalpage_setup('ethosenroltoolethoscreatepsuedocourses');
//
//    $sitecontext = context_system::instance();
//    $site = get_site();
//
//    $strcreatepsuedocourses = get_string('createpsuedocourses', 'enrol_ethos');
//
//    $returnurl = new moodle_url('/enrol/ethos/tools/createpsuedocourses.php');
//
//    echo $OUTPUT->header();
//
//    if ($createpsuedocourses and confirm_sesskey()) {
//        require_capability('moodle/user:update', $sitecontext);
//
//        $trace = new html_progress_trace();
//
//        $processingService= new \enrol_ethos\services\processing_service($trace);
//        $processingService->create_psuedo_courses();
//    }
//
//    // Carry on with the user listing
//    $context = context_system::instance();
//
//    flush();
//
//    $button = '';
//
//    // Process users button
//    if (is_siteadmin($USER)) {
//        $url = new moodle_url($returnurl, array('createpsuedocourses'=>1, 'sesskey'=>sesskey()));
//        $button = html_writer::link($url, $strcreatepsuedocourses . ' ' . $OUTPUT->pix_icon('t/go', $strcreatepsuedocourses));
//    }
//
//    echo html_writer::start_tag('div', array('class'=>'no-overflow'));
//    echo $button;
//    echo html_writer::end_tag('div');
//
//    echo $OUTPUT->footer();