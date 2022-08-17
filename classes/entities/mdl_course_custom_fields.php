<?php
namespace enrol_ethos\entities;

class mdl_course_custom_fields
{
    // Academic Program
    public string $apCode;
    public string $apGuid;
    public string $apLevel;
    public string $apLevelGuid;
    public string $apCredentialsCode;
    public string $apCredentialsType;
    public string $apCredentialsGuid;
    public string $apDisciplines;
    public string $apDisciplinesGuids;
    public string $apDisciplinesDepartment;
    public string $apOwners;
    public string $apOwnersGuids;
    public string $apSiteCode;
    public string $apSiteGuid;
    public string $apStartDate;
    public string $apEndDate;
    public string $apStatus;
    public string $apProgrammeLeads;
    public string $apCourseCoordinators;
    public string $apProgrammeAdministrators;
    public string $apDegreeApprenticeshipFlag;
    public string $apUmpFlag;
    public string $apFranchiseType1;

    // Section
    public string $sectionCode;
    public string $sectionLevel;
    public string $sectionLevelGuid;
    public string $sectionAcademicYear;
    public string $sectionAcademicYearGuid;
    public string $sectionTerm;
    public string $sectionTermGuid;
    public string $sectionPTerm;
    public string $sectionPTermGuid;
    public string $sectionStartDate;
    public string $sectionEndDate;
    public string $sectionArchiveDate;
    public string $sectionRun;
    public string $sectionOwningInstitutionUnits;
    public string $sectionOwningInstitutionUnitsGuids;
    public string $sectionSiteCode;
    public string $sectionSiteGuid;
    public string $sectionGuid;

    public function populateObject(array $customData) {
        $this->apCode = $customData['ap_code'];
        $this->apGuid = $customData['ap_guid'];
        $this->apLevel = $customData['ap_level'];
        $this->apLevelGuid = $customData['ap_level_guid'];
        $this->apCredentialsCode = $customData['ap_credentials_code'];
        $this->apCredentialsType = $customData['ap_credentials_type'];
        $this->apCredentialsGuid = $customData['ap_credentials_guid'];
        $this->apDisciplines = $customData['ap_disciplines'];
        $this->apDisciplinesGuids = $customData['ap_disciplines_guid'];
        $this->apDisciplinesDepartment = $customData['ap_disciplines_department'];
        $this->apOwners = $customData['ap_owners'];
        $this->apOwnersGuids = $customData['ap_owners_guids'];
        $this->apSiteCode = $customData['ap_site_code'];
        $this->apSiteGuid = $customData['ap_site_guid'];
        $this->apStartDate = $customData['ap_startdate'];
        $this->apEndDate = $customData['ap_enddate'];
        $this->apStatus = $customData['ap_status'];
        $this->apProgrammeLeads = $customData['ap_programme_leads'];
        $this->apCourseCoordinators = $customData['ap_course_coordinators'];
        $this->apProgrammeAdministrators = $customData['ap_programme_administrators'];
        $this->apDegreeApprenticeshipFlag = $customData['ap_degree_apprenticeship_flag'];
        $this->apUmpFlag = $customData['ap_ump_flag'];
        $this->apFranchiseType1 = $customData['ap_franch_typ_1'];
        $this->sectionCode = $customData['section_code'];
        $this->sectionLevel = $customData['section_level'];
        $this->sectionLevelGuid = $customData['section_level_guid'];
        $this->sectionAcademicYear = $customData['section_academic_year'];
        $this->sectionAcademicYearGuid = $customData['section_academic_year_guid'];
        $this->sectionTerm = $customData['section_term'];
        $this->sectionTermGuid = $customData['section_term_guid'];
        $this->sectionPTerm = $customData['section_pterm'];
        $this->sectionPTermGuid = $customData['section_pterm_guid'];
        $this->sectionStartDate = $customData['section_start_date'];
        $this->sectionEndDate = $customData['section_end_date'];
        $this->sectionArchiveDate = $customData['section_archive_date'];
        $this->sectionRun = $customData['section_run'];
        $this->sectionOwningInstitutionUnits = $customData['section_owningInstitutionUnits'];
        $this->sectionOwningInstitutionUnitsGuids = $customData['section_owningInstitutionUnits_guids'];
        $this->sectionSiteCode = $customData['section_site_code'];
        $this->sectionSiteGuid = $customData['section_site_guid'];
        $this->sectionGuid = $customData['section_guid'];
}
}