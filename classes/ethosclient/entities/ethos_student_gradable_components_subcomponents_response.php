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

class ethos_student_gradable_components_subcomponents_response {
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $assessmentType;
    public string $componentId;
    public string $crn;

    /**
     * @var ethos_student_gradable_components_subcomponents_response_success[]
     */
    public array $successList;

    /**
     * @param object[] $successObjs
     */
    public function setSuccesses(array $successObjs)
    {
        if (!empty($successObj)){
            foreach($successObjs as $successObj) {
                $this->successList[] = new ethos_student_gradable_components_subcomponents_response_success($successObj);
            }
        } else {
            $this->successList[] = "";
        }
    }

    /**
     * @var ethos_student_gradable_components_subcomponents_response_failure[]
     */
    public array $failureList;

    /**
     * @param object[] $failureObjs
     */
    public function setFailures(array $failureObjs)
    {
        if (!empty($failureObjs)) {
            foreach($failureObjs as $failureObj) {
                $this->failureList[] = new ethos_student_gradable_components_subcomponents_response_failure($failureObj);
            }
        } else {
            $this->failureList[] = "";
        }
    }

    public string $term;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->assessmentType = $data->assessmentType;
        $this->componentId = $data->componentId;
        $this->crn = $data->crn;
        if(isset($data->successList)) {
            $this->setSuccesses($data->successList);
        }
        if(isset($data->failureList)) {
            $this->setFailures($data->failureList);
        }
        $this->term = $data->term;
    }
}
