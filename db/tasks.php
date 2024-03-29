<?php
// This file is part of Moodle - http://moodle.org/
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

/**
 * Task definition for enrol_ldap.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @package   enrol_ldap
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => 'enrol_ethos\task\process_ethos_updates',
        'blocking' => 0,
        'minute' => '45',
        'hour' => '17',
        'day' => '*',
        'month' => '1,7',
        'dayofweek' => '0',
    ],
    [
        'classname' => 'enrol_ethos\task\process_new_users',
        'blocking' => 0,
        'minute' => '45',
        'hour' => '17',
        'day' => '*',
        'month' => '1,7',
        'dayofweek' => '0',
    ],
    [
        'classname' => 'enrol_ethos\task\synchronise_courses',
        'blocking' => 0,
        'minute' => '45',
        'hour' => '17',
        'day' => '*',
        'month' => '1,7',
        'dayofweek' => '0',
    ],
    [
        'classname' => 'enrol_ethos\task\house_keeping',
        'blocking' => 0,
        'minute' => '45',
        'hour' => '17',
        'day' => '*',
        'month' => '1,7',
        'dayofweek' => '0',
    ],
    [
        'classname' => 'enrol_ethos\task\deprecation_detector',
        'blocking' => 0,
        'minute' => '45',
        'hour' => '17',
        'day' => '*',
        'month' => '1,7',
        'dayofweek' => '0',
    ]
];