<?php
//
//    require_once('../../../config.php');
//    require_once($CFG->libdir.'/adminlib.php');
//    require_once($CFG->libdir.'/authlib.php');
//    require_once($CFG->dirroot.'/user/lib.php');
//
//    $processnewusers  = optional_param('processnewusers', 0, PARAM_INT);
//
//    admin_externalpage_setup('ethosenroltoolethosprocessnewusers');
//
//    $sitecontext = context_system::instance();
//    $site = get_site();
//
//    $strprocessnewusers = get_string('processnewusers', 'enrol_ethos');
//
//    $returnurl = new moodle_url('/enrol/ethos/tools/processnewusers.php');
//
//    echo $OUTPUT->header();
//
//    if ($processnewusers and confirm_sesskey()) {
//        require_capability('moodle/user:update', $sitecontext);
//
//        $trace = new html_progress_trace();
//
//        $processingService= new \enrol_ethos\services\processing_service($trace);
//        $processingService->process_new_users();
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
//        $url = new moodle_url($returnurl, array('processnewusers'=>1, 'sesskey'=>sesskey()));
//        $button = html_writer::link($url, $strprocessnewusers . ' ' . $OUTPUT->pix_icon('t/go', $strprocessnewusers));
//    }
//
//    echo html_writer::start_tag('div', array('class'=>'no-overflow'));
//    echo $button;
//    echo html_writer::end_tag('div');
//
//    echo $OUTPUT->footer();