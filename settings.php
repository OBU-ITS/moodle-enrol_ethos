<?php
// This file is part of the Ethos Enrol plugin for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

$ADMIN->add('reports', new admin_category('obureports', 'Ethos Notifications'));

$ADMIN->add('obureports', new admin_externalpage('reportethosnotifications', 'Run reports',
    $CFG->wwwroot . "/enrol/ethos/reports/index.php", 'report/log:view'));

$ADMIN->add('obureports', new admin_externalpage('reportethosbackfill', 'Back fill reports',
    $CFG->wwwroot . "/enrol/ethos/reports/backfill.php", 'report/log:view'));

$hidden = $settings->hidden;

$settings = new admin_category('ethosenrolsettingscat', get_string('pluginname', 'enrol_ethos'), $hidden);

$settingsethos = new admin_settingpage('enrolsettingsethos', get_string('settings'), 'moodle/site:config');

if ($ADMIN->fulltree) {

    $plugin = new stdClass();
    include($CFG->dirroot.'/enrol/ethos/version.php');

    $a = new stdClass();
    $a->version = $plugin->version;
    $a->toolslink = $CFG->wwwroot.'/enrol/ethos/tools';

    $settingsethos->add(new admin_setting_heading('enrol_ethos_settings', '', get_string('header', 'enrol_ethos', $a)));


    // Security --------------------------------------------------------------------------------.
    $settingsethos->add(new admin_setting_heading('enrol_ethos_security', get_string('livesettings', 'enrol_ethos'),
            ''));

    $settingsethos->add(new admin_setting_configtext('enrol_ethos/ethosapikey', get_string('ethosapikey', 'enrol_ethos'),
            get_string('ethosapikeyhelp', 'enrol_ethos'), ''));

}

 $settings->add('ethosenrolsettingscat', $settingsethos);

//$settings->add('ethosenrolsettingscat', new admin_category('ethosenroltoolsscat',
//        get_string('tools', 'enrol_ethos'), false));
//
//$settings->add("ethosenroltoolsscat", new admin_externalpage('ethosenroltoolethosprocessuser', get_string('page_processuser', 'enrol_ethos'),
//        "$CFG->wwwroot/enrol/ethos/tools/processuser.php", "moodle/role:manage"));
//
//$settings->add("ethosenroltoolsscat", new admin_externalpage('ethosenroltoolethosprocessnewusers', get_string('page_processnewusers', 'enrol_ethos'),
//        "$CFG->wwwroot/enrol/ethos/tools/processnewusers.php", "moodle/role:manage"));
//
//$settings->add("ethosenroltoolsscat", new admin_externalpage('ethosenroltoolethosprocessallusers', get_string('page_processallusers', 'enrol_ethos'),
//        "$CFG->wwwroot/enrol/ethos/tools/processallusers.php", "moodle/role:manage"));
//
//$settings->add("ethosenroltoolsscat", new admin_externalpage('ethosenroltoolethoscheckmessages', get_string('page_checkmessages', 'enrol_ethos'),
//        "$CFG->wwwroot/enrol/ethos/tools/checkmessages.php", "moodle/role:manage"));
//
//$settings->add("ethosenroltoolsscat", new admin_externalpage('ethosenroltoolethoscreatepsuedocourses', get_string('page_createpsuedocourses', 'enrol_ethos'),
//        "$CFG->wwwroot/enrol/ethos/tools/createpsuedocourses.php", "moodle/role:manage"));