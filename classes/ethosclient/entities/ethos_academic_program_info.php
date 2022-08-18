<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\providers\ethos_academic_credential_provider;
use enrol_ethos\ethosclient\providers\ethos_academic_level_provider;
use enrol_ethos\ethosclient\providers\ethos_educational_institution_unit_provider;
use enrol_ethos\ethosclient\providers\ethos_site_provider;
use enrol_ethos\ethosclient\services\ethos_academic_program_discipline_service;
use enrol_ethos\ethosclient\services\ethos_section_title_service;

class ethos_academic_program_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    //attributes
    public string $code;
    public string $id;
    public string $ump = "X"; // TODO
    public string $umpJoint = "X"; // TODO
    public string $majorFullTitle = "XXX"; // TODO
    public string $sandwich = "X"; // TODO
    public string $atas = "X"; // TODO
    public string $status;
    public string $startOn;
    public string $endOn;


    //single ref
    private string $academicLevelId;
    private ?ethos_academic_level_info $academicLevel = null;
    public function getAcademicLevelId() : string {
        return $this->academicLevelId;
    }
    public function setAcademicLevelId(string $id) {
        $this->academicLevelId = $id;
        $this->academicLevel = null;
    }
    public function getAcademicLevel() : ethos_academic_level_info
    {
        if(!$this->academicLevel) {
            $provider = ethos_academic_level_provider::getInstance();
            $this->academicLevel = $provider->get($this->academicLevelId);
        }

        return $this->academicLevel;
    }

    // multiple ref
    private array $academicCredentialIds = array();
    private ?array $academicCredentials = null;
    public function getAcademicCredentialIds() : array {
        return $this->academicCredentialIds;
    }
    public function setAcademicCredentialIds(array $objs) {
        $this->academicCredentialIds = array();
        foreach($objs as $obj) {
            $this->academicCredentialIds[] = $obj->id;
        }
        $this->academicCredentials = null;
    }

    /**
     * @return ethos_academic_credential_info[]
     */
    public function getAcademicCredentials() : array
    {
        if(!$this->academicCredentials) {
            $provider = ethos_academic_credential_provider::getInstance();
            $this->academicCredentials = array();
            foreach($this->academicCredentialIds as $academicCredentialId) {
                $this->academicCredentials[] = $provider->get($academicCredentialId);
            }
        }

        return $this->academicCredentials;
    }

    //multiple ref
    private array $programOwnerIds = array();
    private ?array $programOwners = null;
    public function getProgramOwnerIds() : array {
        return $this->programOwnerIds;
    }
    public function setProgramOwnerIds(array $objs) {
        $this->programOwnerIds = array();
        foreach($objs as $obj) {
            $this->programOwnerIds[] = $obj->id;
        }
        $this->programOwners = null;
    }

    /**
     * @return ethos_educational_institution_unit_info[]
     */
    public function getProgramOwners() : array
    {
        if(!$this->programOwners) {
            $provider = ethos_educational_institution_unit_provider::getInstance();
            $this->programOwners = array();
            foreach($this->programOwnerIds as $programOwnerId) {
                $this->programOwners[] = $provider->get($programOwnerId);
            }
        }

        return $this->programOwners;
    }

    //multiple ref
    private array $siteIds = array();
    private ?array $sites = null;
    public function getSiteIds() : array {
        return $this->siteIds;
    }
    public function setSiteIds(array $objs) {
        $this->siteIds = array();
        foreach($objs as $obj) {
            $this->siteIds[] = $obj->id;
        }
        $this->sites = null;
    }

    /**
     * @return ethos_site_info[]
     */
    public function getSites() : array
    {
        if(!$this->sites) {
            $provider = ethos_site_provider::getInstance();
            $this->sites = array();
            foreach($this->siteIds as $siteId) {
                $this->sites[] = $provider->get($siteId);
            }
        }

        return $this->sites;
    }

    //multiple objs
    /**
     * @var ethos_academic_program_info_discipline[]
     */
    public array $disciplines;

    /**
     * @param object[] $disciplineObjs
     */
    private function setDisciplines(array $disciplineObjs)
    {
        $service = ethos_academic_program_discipline_service::getInstance();

        $this->disciplines = array();
        foreach($disciplineObjs as $disciplineObj) {
            $this->disciplines[] = $service->get($disciplineObj);
        }
    }


    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->code = $data->code;
        $this->status = $data->status;
        $this->startOn = $data->startOn;
        $this->endOn = $data->endOn; // TODO Jock check what to do if there is no end on?
        $this->setAcademicCredentialIds($data->academicCredentials);
        $this->setDisciplines($data->disciplines);
        $this->setProgramOwnerIds($data->programOwners);
        $this->setSiteIds($data->sites);

        if(isset($data->academicLevel)) {
            $this->setAcademicLevelId($data->academicLevel->id);
        }
    }
}