<?php

namespace enrol_ethos\services;

use core_course\customfield\course_handler;
use enrol_ethos\repositories\db_profile_category_repository;
use enrol_ethos\repositories\db_profile_field_repository;
use enrol_ethos\services\moodle\mdl_course_custom_field_service;

class obu_additional_field_service
{
    private mdl_course_custom_field_service $courseCustomFieldService;
    private profile_field_service $profileFieldService;

    private function __construct()
    {
        global $DB;

        $this->courseCustomFieldService = mdl_course_custom_field_service::getInstance();

        $profileFieldRepository = new db_profile_field_repository($DB);
        $profileCategoryRepository = new db_profile_category_repository($DB);
        $this->profileFieldService = new profile_field_service($profileFieldRepository, $profileCategoryRepository);
    }

    private static ?obu_additional_field_service $instance = null;
    public static function getInstance() : obu_additional_field_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function ensureAdditionalFields() {
        $this->ensureCourseFields();
        $this->ensureUserFields();
    }

    private function ensureCourseFields() {
        $visibleCourseCategory = $this->courseCustomFieldService->ensureCustomFieldCategory("Academic Program Data");
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Code", "ap_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Level", "ap_level", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Credentials Code", "ap_credentials_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Credentials Type", "ap_credentials_type", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Disciplines", "ap_disciplines", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Owners", "ap_owners", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Site Code", "ap_site_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Start Date", "ap_startdate", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "End Date", "ap_enddate", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Status", "ap_status", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Programme Leads", "ap_programme_leads", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Course Coordinators", "ap_course_coordinators", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Programme Administrators", "ap_programme_administrators", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Degree Apprenticeship Flag", "ap_degree_apprenticeship_flag", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Undergraduate Modular Program", "ap_ump_flag", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Franchised", "ap_franch_typ_1", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);

        $hiddenCourseCategory = $this->courseCustomFieldService->ensureCustomFieldCategory("Academic Program Data (Hidden)");
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Guid", "ap_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Level Id", "ap_level_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Credentials Id", "ap_credentials_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Disciplines Id", "ap_disciplines_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Disciplines Department", "ap_disciplines_department", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Owners Ids", "ap_owners_guids", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Site Id", "ap_site_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);

        $visibleCourseCategory = $this->courseCustomFieldService->ensureCustomFieldCategory("Section Data");
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Code", "section_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Level", "section_level", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Level Id", "section_level_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Academic Year", "section_academic_year", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Term", "section_term", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Part Term", "section_pterm", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Start Date", "section_start_date", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "End Date", "section_end_date", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Archive Date", "section_archive_date", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Run", "section_run", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Owning Institution Units", "section_owningInstitutionUnits", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Site Code", "section_site_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);

        $hiddenCourseCategory = $this->courseCustomFieldService->ensureCustomFieldCategory("Section Data (Hidden)");
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Academic Year Id", "section_academic_year_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Term Id", "section_term_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Part Term Id", "section_pterm_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Owning Institution Units Ids", "section_owningInstitutionUnits_guids", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Site Id", "section_site_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
    }

    private function ensureUserFields() {
        // TODO : Revise this
        $this->profileFieldService->addDefaultCategory();
        $this->profileFieldService->addDefaultFields();
    }
}