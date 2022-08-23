<?php

use core_course\customfield\course_handler;
use enrol_ethos\services\moodle\mdl_course_custom_field_service;

require_once('../../../config.php');
require_once($CFG->libdir.'/weblib.php');

$courseCustomFieldService = mdl_course_custom_field_service::getInstance();

$visibleCourseCategory = $courseCustomFieldService->ensureCustomFieldCategory("Academic Program Data");
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Code", "ap_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Level", "ap_level", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Credentials Code", "ap_credentials_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Credentials Type", "ap_credentials_type", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Disciplines", "ap_disciplines", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Owners", "ap_owners", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Site Code", "ap_site_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Start Date", "ap_startdate", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "End Date", "ap_enddate", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Status", "ap_status", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Programme Leads", "ap_programme_leads", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Course Coordinators", "ap_course_coordinators", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Programme Administrators", "ap_programme_administrators", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Degree Apprenticeship Flag", "ap_degree_apprenticeship_flag", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Undergraduate Modular Program", "ap_ump_flag", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Franchised", "ap_franch_typ_1", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);

$hiddenCourseCategory = $courseCustomFieldService->ensureCustomFieldCategory("Academic Program Data (Hidden)");
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Guid", "ap_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Level Id", "ap_level_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Credentials Id", "ap_credentials_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Disciplines Id", "ap_disciplines_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Disciplines Department", "ap_disciplines_department", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Owners Ids", "ap_owners_guids", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Site Id", "ap_site_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);

$visibleCourseCategory = $courseCustomFieldService->ensureCustomFieldCategory("Section Data");
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Code", "section_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Level", "section_level", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Level Id", "section_level_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Academic Year", "section_academic_year", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Term", "section_term", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Part Term", "section_pterm", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Start Date", "section_start_date", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "End Date", "section_end_date", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Archive Date", "section_archive_date", \customfield_date\field_controller::TYPE, 0, 0, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Run", "section_run", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Owning Institution Units", "section_owningInstitutionUnits", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Site Code", "section_site_code", \customfield_text\field_controller::TYPE, 50, 200, course_handler::VISIBLETOTEACHERS);

$hiddenCourseCategory = $courseCustomFieldService->ensureCustomFieldCategory("Section Data (Hidden)");
$courseCustomFieldService->ensureCustomField($hiddenCourseCategory, "Academic Year Id", "section_academic_year_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Term Id", "section_term_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Part Term Id", "section_pterm_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Owning Institution Units Ids", "section_owningInstitutionUnits_guids", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);
$courseCustomFieldService->ensureCustomField($visibleCourseCategory, "Site Id", "section_site_guid", \customfield_text\field_controller::TYPE, 50, 200, course_handler::NOTVISIBLE);