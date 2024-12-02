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

namespace enrol_ethos\ethosclient\entities;

class ethos_student_gradable_components_subcomponents_info
{
    public function __construct()
    {
        $this->grades = array();
    }

    public string $assessmentType;
    public string $crn;
    public string $term;
    public string $componentId;

    /**
     * @var ethos_student_gradable_components_subcomponents_info_grade[]
     */
    public array $grades;

    /**
     * @param object[] $gradesObjs
     */
    public function setGrades(array $gradesObjs)
    {
        foreach($gradesObjs as $gradesObj) {
            $this->grades[] = $gradesObj;
        }
    }

    public function setGrade(ethos_student_gradable_components_subcomponents_info_grade $gradesObj)
    {
        $this->grades[] = $gradesObj;
    }
}
