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

class ethos_student_gradable_components_subcomponents_response_success
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $bannerId;
    public string $grade;
    public string $score;
    public string $currentReason;
    public string $comment;
    public string $completedDate;
    public string $extensionDate;
    public string $successMessage;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->bannerId = $data->bannerId;
        $this->grade = $data->grade;
        $this->score = $data->score;
        $this->currentReason = $data->currentReason;
        $this->comment = $data->comment ?? "";
        $this->completedDate = $data->completedDate;
        $this->extensionDate = $data->extensionDate ?? "";
        $this->successMessage = $data->successMessage;
    }
}
