<?php
//
//    require_once('../../../config.php');
//    require_once($CFG->libdir.'/adminlib.php');
//    require_once($CFG->libdir.'/authlib.php');
//    require_once($CFG->dirroot.'/user/lib.php');
//
//    $processallusers  = optional_param('processallusers', 0, PARAM_INT);
//
//    admin_externalpage_setup('ethosenroltoolethosprocessallusers');
//
//    $sitecontext = context_system::instance();
//    $site = get_site();
//
//    $strprocessallusers = get_string('processallusers', 'enrol_ethos');
//
//    $returnurl = new moodle_url('/enrol/ethos/tools/processallusers.php');
//
//    echo $OUTPUT->header();
//
//    if ($processallusers and confirm_sesskey()) {
//        require_capability('moodle/user:update', $sitecontext);
//
//        $trace = new html_progress_trace();
//
////        $processingService= new \enrol_ethos\services\processing_service($trace);
////        $processingService->process_all_users();
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
//        $url = new moodle_url($returnurl, array('processallusers'=>1, 'sesskey'=>sesskey()));
//        $button = html_writer::link($url, $strprocessallusers . ' ' . $OUTPUT->pix_icon('t/go', $strprocessallusers));
//    }
//
//    echo html_writer::start_tag('div', array('class'=>'no-overflow'));
//    echo $button;
//    echo html_writer::end_tag('div');
//
//    echo $OUTPUT->footer();