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
 * your older installtion to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the functions defined in lib/ddllib.php
 */

// TODO update older upgrade code?

function xmldb_enrol_ethos_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;

    $dbman = $DB->get_manager();

    $result = true;

    if ($result && $oldversion < 2019061101) {
        $profileFieldRepository = new \enrol_ethos\repositories\db_profile_field_repository($DB);
        $profileCategoryRepository = new \enrol_ethos\repositories\db_profile_category_repository($DB);
        $profileFieldService = new \enrol_ethos\services\profile_field_service($profileFieldRepository, $profileCategoryRepository);
    
        $profileFieldService->addDefaultFields();            
    }
    return $result;
}