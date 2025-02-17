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
 * ${PLUGINNAME} file description here.
 *
 * @package    ${PLUGINNAME}
 * @copyright  2024 p0091841 <${USEREMAIL}>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_student_gradable_components_subcomponents_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;
use ethos_student_gradable_components_subcomponents_response;

class ethos_student_gradable_components_subcomponents_provider extends ethos_provider
{
    const VERSION = 'v1.0.0';
    const PATH = 'student-gradable-components-subcomponents';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
    }

    private static ?ethos_student_gradable_components_subcomponents_provider $instance = null;
    public static function getInstance() : ethos_student_gradable_components_subcomponents_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function put(ethos_student_gradable_components_subcomponents_info $obj) {
        if(!isset($obj->grades)) {
            return null;
        }

        $item = $this->putGradesToEthos($obj);

        if(!$item || isset($item->errors)) {
            return null;
        }

        return $this->convert($item);
    }

    private function convert(object $item) : ethos_student_gradable_components_subcomponents_response {
        return new ethos_student_gradable_components_subcomponents_response($item);
    }
}
