<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_academic_level_provider;
use enrol_ethos\ethosclient\providers\ethos_academic_period_provider;
use enrol_ethos\ethosclient\providers\ethos_course_provider;
use enrol_ethos\ethosclient\providers\ethos_educational_institution_unit_provider;
use enrol_ethos\ethosclient\providers\ethos_site_provider;
use enrol_ethos\ethosclient\services\ethos_owning_institution_unit_service;

class ethos_section_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    // Attributes
    public string $id; // required
    public string $code;
    public string $number;
    public string $startOn; // required
    public string $endOn;
    public string $instructionalDeliveryMethod;

    // Single references
    private string $courseId;  // required
    private ?ethos_course_info $course = null;
    public function getCourseId() : string {
        return $this->courseId;
    }
    public function setCourseId(string $id) {
        $this->courseId = $id;
        $this->course = null;
    }
    public function getCourse() : ethos_course_info
    {
        if(!$this->course) {
            $provider = ethos_course_provider::getInstance();
            $this->course = $provider->get($this->courseId);
        }

        return $this->course;
    }

    private string $siteId;
    private ?ethos_site_info $site = null;
    public function getSiteId() : string {
        return $this->siteId;
    }
    public function setSiteId(string $id) {
        $this->siteId = $id;
        $this->site = null;
    }
    public function getSite() : ethos_site_info
    {
        if(!$this->site) {
            $provider = ethos_site_provider::getInstance();
            $this->site = $provider->get($this->siteId);
        }

        return $this->site;
    }

    private string $academicPeriodId;
    private ?ethos_academic_period_info $academicPeriod = null;
    public function getAcademicPeriodId() : string {
        return $this->academicPeriodId;
    }
    public function setAcademicPeriodId(string $id) {
        $this->academicPeriodId = $id;
        $this->academicPeriod = null;
    }
    public function getAcademicPeriod() : ethos_academic_period_info
    {
        if(!$this->academicPeriod) {
            $provider = ethos_academic_period_provider::getInstance();
            $this->academicPeriod = $provider->get($this->academicPeriodId);
        }

        return $this->academicPeriod;
    }

    // Multiple references
    private array $academicLevelIds = array();
    private ?array $academicLevels = null;
    public function getAcademicLevelIds() : array {
        return $this->academicLevelIds;
    }
    public function setAcademicLevelIds(array $ids) {
        $this->academicLevelIds = $ids;
        $this->academicLevels = null;
    }
    public function getAcademicLevels() : array
    {
        if(!$this->academicLevels) {
            $provider = ethos_academic_level_provider::getInstance();
            $this->academicLevels = array();
            foreach($this->academicLevelIds as $academicLevelId) {
                $this->academicLevels[] = $provider->get($academicLevelId);
            }
        }

        return $this->academicLevels;
    }

    private array $owningInstitutionUnitObjs = array();
    private ?array $owningInstitutionUnits = null;
    public function getOwningInstitutionUnitObjs() : array {
        return $this->owningInstitutionUnitObjs;
    }
    public function setOwningInstitutionUnitObjs(array $ids) {
        $this->owningInstitutionUnitObjs = $ids;
        $this->owningInstitutionUnits = null;
    }
    public function getOwningInstitutionUnit() : array
    {
        if(!$this->owningInstitutionUnits) {
            $service = ethos_owning_institution_unit_service::getInstance();
            $this->owningInstitutionUnits = array();
            foreach($this->owningInstitutionUnitObjs as $owningInstitutionUnitObj) {
                if(!isset($owningInstitutionUnitObj->institutionUnit)) {
                    continue;
                }

                $this->owningInstitutionUnits[] = $service->get($owningInstitutionUnitObj->institutionUnit->id, $owningInstitutionUnitObj->ownershipPercentage);
            }
        }

        return $this->owningInstitutionUnits;
    }

    // TODO : Joe
    public array $titleIds;

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->code = $data->code;
        $this->number = $data->number;
        $this->startOn = $data->startOn;
        $this->endOn = $data->endOn;
        $this->instructionalDeliveryMethod = $data->instructionalDeliveryMethod; // TODO : Check

       if(isset($data->course)) {
            $this->setCourseId($data->course->id);
        }
        if(isset($data->site)) {
            $this->setSiteId($data->site->id);
        }
        if(isset($data->academicPeriod)) {
            $this->setAcademicPeriodId($data->academicPeriod->id);
        }


    }
}