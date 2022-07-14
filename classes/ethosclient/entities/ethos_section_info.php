<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_academic_period_provider;
use enrol_ethos\ethosclient\providers\ethos_course_provider;
use enrol_ethos\ethosclient\providers\ethos_site_provider;

class ethos_section_info
{
    public function __construct(object $data)
    {
        // TODO : Joe
    }

    // Attributes
    public string $id; // required
    public string $code;
    public string $number;
    public string $startOn; // required
    public string $endOn;
    public string $status;
    public string $instructionalDeliveryMethod;

    // Single references
    public string $courseId;  // required
    private ?ethos_course_info $course = null;
    public function getCourse() : ethos_course_info
    {
        if(!$this->course) {
            $provider = ethos_course_provider::getInstance();
            $this->course = $provider->get($this->courseId);
        }

        return $this->course;
    }

    public string $siteId;
    private ?ethos_site_info $site = null;
    public function getSite() : ethos_site_info
    {
        if(!$this->site) {
            $provider = ethos_site_provider::getInstance();
            $this->site = $provider->get($this->siteId);
        }

        return $this->site;
    }

    public string $academicPeriodId;
    private ?ethos_academic_period_info $academicPeriod = null;
    public function getAcademicPeriod() : ethos_academic_period_info
    {
        if(!$this->academicPeriod) {
            $provider = ethos_academic_period_provider::getInstance();
            $this->academicPeriod = $provider->get($this->courseId);
        }

        return $this->academicPeriod;
    }

    // Multiple references
    // TODO : Joe
    public array $academicLevels;
    public array $owningInstitutionUnits;
    public array $titles;
    public array $descriptions;
}