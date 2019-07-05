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
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     profilefield_ethosuser
 * @category    upgrade
 * @copyright   2019 Your Name <you@example.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Custom code to be run on installing the plugin.
 */

function xmldb_enrol_ethos_install() {
    //Add category and fields to all users
    
    global $DB;
    $profileFieldRepository = new \enrol_ethos\repositories\db_profile_field_repository($DB);
    $profileCategoryRepository = new \enrol_ethos\repositories\db_profile_category_repository($DB);
    $profileFieldService = new \enrol_ethos\services\profile_field_service($profileFieldRepository, $profileCategoryRepository);

    $profileFieldService->addDefaultCategory();
    $profileFieldService->addDefaultFields();

    return true;
}
