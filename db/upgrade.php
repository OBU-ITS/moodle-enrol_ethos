<?php
// This file is part of the Banner/LMB plugin for Moodle - http://moodle.org/
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


/* This file keeps track of upgrades to
 * the authorize enrol plugin
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the functions defined in lib/ddllib.php
 */

use enrol_ethos\services\obu_additional_field_service;

function xmldb_enrol_ethos_upgrade($oldversion) {

    global $DB;

    $dbman = $DB->get_manager();

    $manager = obu_additional_field_service::GetInstance();
    $manager->ensureAdditionalFields();

    if($oldversion < 2022060901) {

        $table = new xmldb_table('ethos_report_run');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('run_time', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        $table->add_field('messages_consumed', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        $table->add_field('messages_processed', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        $table->add_field('users_created', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        $table->add_field('users_updated', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        $table->add_field('elapsed_time', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        $table->add_field('last_consumed_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $table = new xmldb_table('ethos_report_action');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('run_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        $table->add_field('action_type', XMLDB_TYPE_CHAR, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '');
        $table->add_field('resource_name', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, '');
        $table->add_field('resource_id', XMLDB_TYPE_CHAR, '36', null, XMLDB_NOTNULL, null, '');
        $table->add_field('resource_description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, '');

        $table->add_key('id', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('report_id', XMLDB_KEY_FOREIGN, array('run_id'), 'ethos_report_run', array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2022060901, 'enrol', 'ethos');
    }

    if($oldversion < 2023012401) {

        $table = new xmldb_table('obu_ethos_request');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('requested', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, '');
        $table->add_field('request_duration', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, '');
        $table->add_field('status', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, '');
        $table->add_field('received_count', XMLDB_TYPE_INTEGER, '7', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        $table->add_field('remaining_count', XMLDB_TYPE_INTEGER, '7', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $table = new xmldb_table('obu_ethos_message');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('request_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        $table->add_field('published', XMLDB_TYPE_CHAR, '29', null, XMLDB_NOTNULL, null, '');
        $table->add_field('resource_name', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, '');
        $table->add_field('resource_id', XMLDB_TYPE_CHAR, '36', null, XMLDB_NOTNULL, null, '');
        $table->add_field('operation', XMLDB_TYPE_CHAR, null, null, XMLDB_NOTNULL, null, '');
        $table->add_field('content_type', XMLDB_TYPE_CHAR, null, null, XMLDB_NOTNULL, null, '');
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, '');

        $table->add_key('id', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('message_request_id', XMLDB_KEY_FOREIGN, array('request_id'), 'obu_ethos_request', array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2023012401, 'enrol', 'ethos');
    }

    return true;
}