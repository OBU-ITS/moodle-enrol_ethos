<?php
namespace enrol_ethos\services;
use enrol_ethos\interfaces\profile_field_repository_interface;
use enrol_ethos\interfaces\profile_category_repository_interface;
use enrol_ethos\entities\profile_category;
use enrol_ethos\entities\profile_field;

class profile_field_service {

    private profile_field_repository_interface $profileFieldRepository;
    private profile_category_repository_interface $profileCategoryRepository;

    private $defaultCategory;

    public function __construct(profile_field_repository_interface $profileFieldRepository, profile_category_repository_interface $profileCategoryRepository) {
        $this->profileFieldRepository = $profileFieldRepository;
        $this->profileCategoryRepository = $profileCategoryRepository;
    }

    private function getDefaultFields() : array {
        $fieldArray = array();

        $defaultCategory = $this->getDefaultCategory();

        //TODO MAKE THIS A CONFIG FILE
        $fieldArray[] = new profile_field('courseCode', 'Course Code', 'Course Code', $defaultCategory->id);
        $fieldArray[] = new profile_field('courseTitle', 'Course Title', 'Course Title', $defaultCategory->id);
        $fieldArray[] = new profile_field('establishmentCode', 'Establishment Code', 'Establishment Code', $defaultCategory->id);
        $fieldArray[] = new profile_field('establishmentTitle', 'Establishment Title', 'Establishment Title', $defaultCategory->id);
        $fieldArray[] = new profile_field('facultyCode', 'Faculty Code', 'Faculty Code', $defaultCategory->id);
        $fieldArray[] = new profile_field('facultyTitle', 'Faculty Title', 'Faculty Title', $defaultCategory->id);
        $fieldArray[] = new profile_field('departmentCode', 'Department Code', 'Department Code', $defaultCategory->id);
        $fieldArray[] = new profile_field('departmentTitle', 'Department Title', 'Department Title', $defaultCategory->id);
        $fieldArray[] = new profile_field('schoolTypeCode', 'School Type Code', 'School Type Code', $defaultCategory->id);
        $fieldArray[] = new profile_field('schoolTypeTitle', 'School Type Title', 'School Type Title', $defaultCategory->id);
        $fieldArray[] = new profile_field('attendanceMode', 'Attendance Mode', 'Attendance Mode', $defaultCategory->id);
        $fieldArray[] = new profile_field('attendanceModeTitle', 'Attendance Mode Title', 'Attendance Mode Title', $defaultCategory->id);
        $fieldArray[] = new profile_field('subjectCode', 'Subject Code', 'Subject Code', $defaultCategory->id);
        $fieldArray[] = new profile_field('subjectTitle', 'Subject Title', 'Subject Title', $defaultCategory->id);
        $fieldArray[] = new profile_field('awardCode', 'Award Code', 'Award Code', $defaultCategory->id);
        $fieldArray[] = new profile_field('awardTitle', 'Award Title', 'Award Title', $defaultCategory->id);
        $fieldArray[] = new profile_field('startDate', 'Start Date', 'Start Date', $defaultCategory->id,'datetime');
        $fieldArray[] = new profile_field('endDate', 'End Date', 'End Date', $defaultCategory->id,'datetime');
        $fieldArray[] = new profile_field('status', 'Status', 'Status', $defaultCategory->id);
        $fieldArray[] = new profile_field('statusTitle', 'Status Title', 'Status Title', $defaultCategory->id);
        $fieldArray[] = new profile_field('graduatedOn', 'Graduated On', 'Graduated On', $defaultCategory->id);
        $fieldArray[] = new profile_field('recognitions', 'Recognitions', 'Recognitions', $defaultCategory->id);
        $fieldArray[] = new profile_field('creditsEarned', 'Credits Earned', 'Credits Earned', $defaultCategory->id);
        $fieldArray[] = new profile_field('academicLevel', 'Academic Level', 'Academic Level', $defaultCategory->id);
        $fieldArray[] = new profile_field('programmes', 'Programmes', 'Programmes', $defaultCategory->id);
        $fieldArray[] = new profile_field('leadProgramOfStudy', 'Lead Program Of Study', 'Lead Program Of Study', $defaultCategory->id);
        $fieldArray[] = new profile_field('bannerGuid', 'Banner Person GUID', 'Banner Person GUID', $defaultCategory->id);
        $fieldArray[] = new profile_field('dyslexic', 'Dyslexic', 'Dyslexic', $defaultCategory->id,'checkbox');
        return $fieldArray;
    }

    private function getDefaultCategoryTemplate() : profile_category {
        return new profile_category('SRS',1);
    }

    private function getDefaultCategory() {
        if (!$this->defaultCategory) {
            $defaultCategory = $this->getDefaultCategoryTemplate();
            $match = $this->profileCategoryRepository->findOne($defaultCategory->name);
            if ($match){
                $this->defaultCategory = $match;
            } else {
                return false;
            }
        }

        return $this->defaultCategory;
    }

    public function deleteDefaultFields() {
        $defaultFields = $this->getDefaultFields();

        foreach($defaultFields as $profileField) {
            $this->profileFieldRepository->remove($profileField);
        }
    }

    public function addDefaultFields() {
        $defaultFields = $this->getDefaultFields();

        foreach($defaultFields as $profileField) {

            if ($dbProfileField = $this->profileFieldRepository->findOne($profileField->shortname)){
                $profileField->id = $dbProfileField->id;
            }

            $this->profileFieldRepository->save($profileField);
        }
    }

    public function addDefaultCategory() {

        $defaultCategory = $this->getDefaultCategory();

        if (!$defaultCategory) {
            $defaultCategory = $this->getDefaultCategoryTemplate();
            $defaultCategory->id = $this->profileCategoryRepository->save($defaultCategory);
        }

        $this->defaultCategory = $defaultCategory;
    }
}