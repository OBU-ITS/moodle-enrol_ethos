<?PHP
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

$string['pluginname'] = 'Ethos Enrol';

$string['description'] = 'This module provides a way to integrate Moodle with Ethos.';

$string['tools'] = 'Ethos Enrol Tools';

$string['header'] = 'You are using Ethos Enrol  Module version {$a->version}.';

$string['logsettings'] = 'Log settings';
$string['logtolocation'] = 'Log file output location (blank for no logging)';
$string['logerrors'] = 'Only log errors';
$string['logpercent'] = 'Log % complete when running batch processes.';

$string['livesettings'] = 'Live Import Settings';
$string['ethosapikey'] = 'Ethos API Key';

$string['coursecategorysettings'] = 'Course and category settings'; 

$string['catselect'] = 'Root category'; 
$string['alumnicourseidnumber'] = 'Alumni course ID Number'; 

$string['aftersaving'] = 'Once you have saved your settings, you may wish to ';
$string['importnow'] = 'import right now';

$string['logtolocationhelp'] = 'This is the location you would like the log file to be saved to. This should be an absolute path on the server. The file specified should already exist, and needs to be writable by the webserver process.';
$string['logerrorshelp'] = 'If this box is checked, only errors will be recorded to the logfile. If it is not checked, all events will be recorded.';

$string['ethosapikeyhelp'] = 'This is the API Key used by this plugin to authenticate to Ethos.';

$string['catselecthelp'] = 'All new courses will be placed under this category (nested into faculty/subject categories as required).'; 
$string['alumnicourseidnumberhelp'] = 'Students due to complete studying in less than 1 year will be automatically enrolled onto this course. Leave blank to disable alumni auto-enrol.'; 

$string['page_processuser'] = 'Process a single user';
$string['page_processallusers'] = 'Process all users';
$string['page_processnewusers'] = 'Process new users';
$string['page_createpsuedocourses'] = 'Create psuedo courses';
$string['page_checkmessages'] = 'Check for Ethos updates';

$string['processuser'] = 'Process';
$string['processallusers'] = 'Process all users';
$string['processnewusers'] = 'Process new users';
$string['createpsuedocourses'] = 'Create psuedo courses';
$string['checkmessages'] = 'Check for Ethos updates';

$string['ethos:enrol'] = 'Enrol users';
$string['ethos:unenrol'] = 'Unenrol users from the course';
$string['ethos:unenrolself'] = 'Unenrol self from the course';
$string['ethos:manage'] = 'Manage user enrolments';