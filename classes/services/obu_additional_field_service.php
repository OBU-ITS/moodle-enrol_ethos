<?php

namespace enrol_ethos\services;

use core_course\customfield\course_handler;
use enrol_ethos\services\moodle\mdl_course_custom_field_service;
use enrol_ethos\services\moodle\mdl_user_profile_field_service;

class obu_additional_field_service
{

    private mdl_course_custom_field_service $courseCustomFieldService;
    private mdl_user_profile_field_service $userProfileFieldService;

    private function __construct()
    {
        $this->courseCustomFieldService = mdl_course_custom_field_service::getInstance();
        $this->userProfileFieldService = mdl_user_profile_field_service::getInstance();
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
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Academic Year", "section_academic_year", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Term", "section_term", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Part Term", "section_pterm", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Start Date", "section_start_date", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "End Date", "section_end_date", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Archive Date", "section_archive_date", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Run", "section_run", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Owning Institution Units", "section_owningInstitutionUnits", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
        $this->courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Site Code", "section_site_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);

        $hiddenCourseCategory = $this->courseCustomFieldService->ensureCustomFieldCategory("Section Data (Hidden)");
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Academic Year Id", "section_academic_year_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Term Id", "section_term_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Part Term Id", "section_pterm_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Owning Institution Units Ids", "section_owningInstitutionUnits_guids", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Site Id", "section_site_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Level Id", "section_level_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
        $this->courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Id", "section_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);

    }

    private function ensureUserFields() {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $visibleUserCategory = $this->userProfileFieldService->ensureCustomFieldCategory("Student Data");
        $this->userProfileFieldService->ensureCustomField($visibleUserCategory, "Adviser", "student_adviser", "text", 30, 200, PROFILE_VISIBLE_TEACHERS);
        $this->userProfileFieldService->ensureCustomField($visibleUserCategory, "Completion Date", "student_completion_date", "datetime", 30, 200, PROFILE_VISIBLE_TEACHERS);
        $this->userProfileFieldService->ensureCustomField($visibleUserCategory, "Status", "student_status", "text", 30, 200, PROFILE_VISIBLE_TEACHERS);

        $hiddenUserCategory = $this->userProfileFieldService->ensureCustomFieldCategory("Student Data (Hidden)");
        $this->userProfileFieldService->ensureCustomField($hiddenUserCategory, "Finance Hold", "finance_hold", "text", 30, 200, PROFILE_VISIBLE_NONE);
        $this->userProfileFieldService->ensureCustomField($hiddenUserCategory, "Academic Hold", "academic_hold", "text", 30, 200, PROFILE_VISIBLE_NONE);
        $this->userProfileFieldService->ensureCustomField($hiddenUserCategory, "Service Needs", "service_needs", "text", 30, 200, PROFILE_VISIBLE_NONE);
        $this->userProfileFieldService->ensureCustomField($hiddenUserCategory, "Guid", "student_guid", "text", 30, 200, PROFILE_VISIBLE_NONE);
        $this->userProfileFieldService->ensureCustomField($hiddenUserCategory, "Academic Programs", "student_academic_programs", "text", 30, 200, PROFILE_VISIBLE_NONE);

        $visibleUserCategory = $this->userProfileFieldService->ensureCustomFieldCategory("Staff Data");
        $this->userProfileFieldService->ensureCustomField($visibleUserCategory, "Is Adviser", "is_adviser_flag", "text", 30, 200, PROFILE_VISIBLE_TEACHERS);
        $this->userProfileFieldService->ensureCustomField($visibleUserCategory, "Is Module Lead", "is_module_lead_flag", "text", 30, 200, PROFILE_VISIBLE_TEACHERS);

        $hiddenUserCategory = $this->userProfileFieldService->ensureCustomFieldCategory("All Persons Data (Hidden)");
        $this->userProfileFieldService->ensureCustomField($hiddenUserCategory, "Person Id", "person_guid", "text", 30, 200, PROFILE_VISIBLE_NONE);
        $this->userProfileFieldService->ensureCustomField($hiddenUserCategory, "PIDM", "pidm", "text", 30, 200, PROFILE_VISIBLE_NONE);
    }
}