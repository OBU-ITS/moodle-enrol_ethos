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

class ethos_student_gradable_components_subcomponents_response {
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $assessmentType;
    public string $crn;
    public string $term;
    public string $componentId;

    /**
     * @var ethos_student_gradable_components_subcomponents_response_success[]
     */
    public array $successList;

    /**
     * @param object[] $successObjs
     */
    public function setSuccesses(array $successObjs)
    {
        foreach($successObjs as $successObj) {
            $this->successList[] = new ethos_student_gradable_components_subcomponents_response_success($successObj);
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
        foreach($failureObjs as $failureObj) {
            $this->failureList[] = new ethos_student_gradable_components_subcomponents_response_failure($failureObj);
        }
    }


    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->assessmentType = $data->assessmentType;
        $this->crn = $data->crn;
        $this->term = $data->term;
        $this->componentId = $data->componentId;

        if(isset($data->successList)) {
            $this->setSuccesses($data->setSuccesses);
        }
        if(isset($data->failureList)) {
            $this->setFailures($data->failureList);
        }
    }
}
