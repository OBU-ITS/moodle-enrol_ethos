<?php

//    require_once('../../../config.php');
//    require_once($CFG->libdir.'/adminlib.php');
//    require_once($CFG->libdir.'/authlib.php');
//    require_once($CFG->dirroot.'/user/filters/lib.php');
//    require_once($CFG->dirroot.'/user/lib.php');
//
//    $sort         = optional_param('sort', 'name', PARAM_ALPHANUM);
//    $dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
//    $page         = optional_param('page', 0, PARAM_INT);
//    $perpage      = optional_param('perpage', 30, PARAM_INT);        // how many per page
//    $processuser  = optional_param('processuser', 0, PARAM_INT);
//
//    admin_externalpage_setup('ethosenroltoolethosprocessuser');
//
//    $sitecontext = context_system::instance();
//    $site = get_site();
//
//    $strshowallusers = get_string('showallusers');
//    $strprocessuser = get_string('processuser', 'enrol_ethos');
//
//    $returnurl = new moodle_url('/enrol/ethos/tools/processuser.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'page'=>$page));
//
//    // The $user variable is also used outside of these if statements.
//    $user = null;
//
//    // create the user filter form
//    $ufiltering = new user_filtering();
//    echo $OUTPUT->header();
//
//    if ($processuser and confirm_sesskey()) {
//        require_capability('moodle/user:update', $sitecontext);
//
//        $trace = new html_progress_trace();
//
//        $processingService= new \enrol_ethos\services\processing_service($trace);
//        $processingService->process_user_by_id($processuser);
//    }
//
//    // Carry on with the user listing
//    $context = context_system::instance();
//    // These columns are always shown in the users list.
//    $requiredcolumns = array('city', 'country', 'lastaccess');
//    // Extra columns containing the extra user fields, excluding the required columns (city and country, to be specific).
//    $extracolumns = get_extra_user_fields($context, $requiredcolumns);
//    // Get all user name fields as an array.
//    $allusernamefields = get_all_user_name_fields(false, null, null, null, true);
//    $columns = array_merge($allusernamefields, $extracolumns, $requiredcolumns);
//
//    foreach ($columns as $column) {
//        $string[$column] = \core_user\fields::get_display_name($column);
//        if ($sort != $column) {
//            $columnicon = "";
//            if ($column == "lastaccess") {
//                $columndir = "DESC";
//            } else {
//                $columndir = "ASC";
//            }
//        } else {
//            $columndir = $dir == "ASC" ? "DESC":"ASC";
//            if ($column == "lastaccess") {
//                $columnicon = ($dir == "ASC") ? "sort_desc" : "sort_asc";
//            } else {
//                $columnicon = ($dir == "ASC") ? "sort_asc" : "sort_desc";
//            }
//            $columnicon = $OUTPUT->pix_icon('t/' . $columnicon, get_string(strtolower($columndir)), 'core',
//                                            ['class' => 'iconsort']);
//
//        }
//        $$column = "<a href=\"processuser.php?sort=$column&amp;dir=$columndir\">".$string[$column]."</a>$columnicon";
//    }
//
//    // We need to check that alternativefullnameformat is not set to '' or language.
//    // We don't need to check the fullnamedisplay setting here as the fullname function call further down has
//    // the override parameter set to true.
//    $fullnamesetting = $CFG->alternativefullnameformat;
//    // If we are using language or it is empty, then retrieve the default user names of just 'firstname' and 'lastname'.
//    if ($fullnamesetting == 'language' || empty($fullnamesetting)) {
//        // Set $a variables to return 'firstname' and 'lastname'.
//        $a = new stdClass();
//        $a->firstname = 'firstname';
//        $a->lastname = 'lastname';
//        // Getting the fullname display will ensure that the order in the language file is maintained.
//        $fullnamesetting = get_string('fullnamedisplay', null, $a);
//    }
//
//    // Order in string will ensure that the name columns are in the correct order.
//    $usernames = order_in_string($allusernamefields, $fullnamesetting);
//    $fullnamedisplay = array();
//    foreach ($usernames as $name) {
//        // Use the link from $$column for sorting on the user's name.
//        $fullnamedisplay[] = ${$name};
//    }
//    // All of the names are in one column. Put them into a string and separate them with a /.
//    $fullnamedisplay = implode(' / ', $fullnamedisplay);
//    // If $sort = name then it is the default for the setting and we should use the first name to sort by.
//    if ($sort == "name") {
//        // Use the first item in the array.
//        $sort = reset($usernames);
//    }
//
//    list($extrasql, $params) = $ufiltering->get_sql_filter();
//    $users = get_users_listing($sort, $dir, $page*$perpage, $perpage, '', '', '',
//            $extrasql, $params, $context);
//    $usercount = get_users(false);
//    $usersearchcount = get_users(false, '', false, null, "", '', '', '', '', '*', $extrasql, $params);
//
//    if ($extrasql !== '') {
//        echo $OUTPUT->heading("$usersearchcount / $usercount ".get_string('users'));
//        $usercount = $usersearchcount;
//    } else {
//        echo $OUTPUT->heading("$usercount ".get_string('users'));
//    }
//
//    $strall = get_string('all');
//
//    $baseurl = new moodle_url('/enrol/tool/processuser.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
//    echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
//
//    flush();
//
//    if (!$users) {
//        $match = array();
//        echo $OUTPUT->heading(get_string('nousersfound'));
//
//        $table = NULL;
//
//    } else {
//
//        $countries = get_string_manager()->get_list_of_countries(true);
//
//        foreach ($users as $key => $user) {
//            if (isset($countries[$user->country])) {
//                $users[$key]->country = $countries[$user->country];
//            }
//        }
//        if ($sort == "country") {
//            // Need to resort by full country name, not code.
//            foreach ($users as $user) {
//                $susers[$user->id] = $user->country;
//            }
//            // Sort by country name, according to $dir.
//            if ($dir === 'DESC') {
//                arsort($susers);
//            } else {
//                asort($susers);
//            }
//            foreach ($susers as $key => $value) {
//                $nusers[] = $users[$key];
//            }
//            $users = $nusers;
//        }
//
//        $table = new html_table();
//        $table->head = array ();
//        $table->colclasses = array();
//        $table->head[] = $fullnamedisplay;
//        $table->attributes['class'] = 'admintable generaltable';
//        foreach ($extracolumns as $field) {
//            $table->head[] = ${$field};
//        }
//        $table->head[] = $city;
//        $table->head[] = $country;
//        $table->head[] = $lastaccess;
//        $table->head[] = $strprocessuser;
//        $table->colclasses[] = 'centeralign';
//        $table->head[] = "";
//        $table->colclasses[] = 'centeralign';
//
//        $table->id = "users";
//        foreach ($users as $user) {
//            $buttons = array();
//            $lastcolumn = '';
//
//            // Process user button
//            if (is_siteadmin($USER)) {
//                $url = new moodle_url($returnurl, array('processuser'=>$user->id, 'sesskey'=>sesskey()));
//                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/go', $strprocessuser));
//            }
//
//            if ($user->lastaccess) {
//                $strlastaccess = format_time(time() - $user->lastaccess);
//            } else {
//                $strlastaccess = get_string('never');
//            }
//            $fullname = fullname($user, true);
//
//            $row = array ();
//            $row[] = "<a href=\"../../../user/view.php?id=$user->id&amp;course=$site->id\">$fullname</a>";
//            foreach ($extracolumns as $field) {
//                $row[] = $user->{$field};
//            }
//            $row[] = $user->city;
//            $row[] = $user->country;
//            $row[] = $strlastaccess;
//            if ($user->suspended) {
//                foreach ($row as $k=>$v) {
//                    $row[$k] = html_writer::tag('span', $v, array('class'=>'usersuspended'));
//                }
//            }
//            $row[] = implode(' ', $buttons);
//            $row[] = $lastcolumn;
//            $table->data[] = $row;
//        }
//    }
//
//    // add filters
//    $ufiltering->display_add();
//    $ufiltering->display_active();
//
//    if (!empty($table)) {
//        echo html_writer::start_tag('div', array('class'=>'no-overflow'));
//        echo html_writer::table($table);
//        echo html_writer::end_tag('div');
//        echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
//    }
//
//    echo $OUTPUT->footer();
