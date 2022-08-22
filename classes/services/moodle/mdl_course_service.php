<?php
namespace enrol_ethos\services\moodle;

use enrol_ethos\entities\mdl_course;
use enrol_ethos\entities\mdl_course_custom_fields;
use enrol_ethos\entities\obu_course_hierarchy_info;
use enrol_ethos\repositories\db_course_repository;
use progress_trace;

class mdl_course_service
{
    private mdl_course_category_service $courseCategoryService;
    private db_course_repository $courseRepo;
    private mdl_course_custom_field_service $courseCustomFieldService;

    private function __construct()
    {
        global $DB;

        $this->courseCategoryService = mdl_course_category_service::getInstance();
        $this->courseRepo = new db_course_repository($DB);
    }

    private static ?mdl_course_service $instance = null;
    public static function getInstance(): mdl_course_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get Module Runs
     *
     * @param int $limit
     * @param int $offset
     * @return mdl_course[] module runs
     */
    public function getModuleRuns(int $limit, int $offset) : array {

    }

    /**
     * Get Programs
     *
     * @param int $limit
     * @param int $offset
     * @return mdl_course[] programs
     */
    public function getPrograms(int $limit, int $offset) : array {
        // TODO : get courses by type
    }

    public function handleCourseCreation(progress_trace $trace, obu_course_hierarchy_info $courseHierarchy, string $keyPrefix = '', ?int $parentId = null) {
        $categoryIdNumber = $this->courseCategoryService->getCategoryId($keyPrefix, $courseHierarchy->currentCategory->codeName);
        $categoryId = $this->courseCategoryService->upsertCourseCategory($trace, $courseHierarchy->currentCategory, $categoryIdNumber, $parentId);

        if($courseHierarchy->hasSubCategories()) {
            $childKeyPrefix = $this->courseCategoryService->getCategoryPrefix($keyPrefix, $courseHierarchy->currentCategory->codeName, $courseHierarchy->currentCategory->alternateCodeName);

            $childrenCategories = $courseHierarchy->getSubCategories();
            foreach ($childrenCategories as $childCategory) {
                $this->handleCourseCreation($trace, $childCategory, $childKeyPrefix, $categoryId);
            }
        }

        $childrenCourses = $courseHierarchy->getCourses();
        foreach($childrenCourses as $childCourse) {
            $childCourse->catid = $categoryId;
            $this->upsertCourse($trace, $childCourse);
        }
    }

    private function upsertCourse(progress_trace $trace, mdl_course $data) {
        $course = $this->courseRepo->findOne($data->idnumber);
        if(!$course) {
            $course = $this->courseRepo->findOneByShortName($data->shortname);
        }

        if($course)
        {
            if($updatedCourse = $this->getUpdatedCourse($course, $data))
            {
                $this->courseRepo->update($updatedCourse);
                $trace->output("Course updated : $data->name ($data->idnumber) ($data->bannerId)");
            }
        }
        else {
            $this->courseRepo->create($data);
            $trace->output("Course created : $data->name ($data->id)");
        }
    }

    private function getUpdatedCourse(mdl_course $currentCourse, mdl_course $newCourse) {
        $hasChanges = false;

        if(strval($currentCourse->idnumber) !== $newCourse->idnumber) {
            $currentCourse->idnumber = $newCourse->idnumber;
            $hasChanges = true;
        }

        if(strval($currentCourse->shortname) !== $newCourse->shortname) {
            $currentCourse->shortname = $newCourse->shortname;
            $hasChanges = true;
        }

        if(strval($currentCourse->name) !== $newCourse->name) {
            $currentCourse->name = $newCourse->name;
            $hasChanges = true;
        }

        if(strval($currentCourse->catid) !== strval($newCourse->catid)) {
            $currentCourse->catid = $newCourse->catid;
            $hasChanges = true;
        }

        if(strval($currentCourse->startdate) !== strval($newCourse->startdate)) {
            $currentCourse->startdate = $newCourse->startdate;
            $hasChanges = true;
        }

        if(strval($currentCourse->enddate) !== strval($newCourse->enddate)) {
            $currentCourse->enddate = $newCourse->enddate;
            $hasChanges = true;
        }

        if($newCourse->courseType === "academicprogram"){
            if($currentCourse->getCustomData()->apCode !== $newCourse->getCustomData()->apCode) {
                $currentCourse->getCustomData()->apCode = $newCourse->getCustomData()->apCode;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apGuid !== $newCourse->getCustomData()->apGuid) {
                $currentCourse->getCustomData()->apGuid = $newCourse->getCustomData()->apGuid;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apLevel !== $newCourse->getCustomData()->apLevel) {
                $currentCourse->getCustomData()->apLevel = $newCourse->getCustomData()->apLevel;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apLevelGuid !== $newCourse->getCustomData()->apLevelGuid) {
                $currentCourse->getCustomData()->apLevelGuid = $newCourse->getCustomData()->apLevelGuid;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apCredentialsCode !== $newCourse->getCustomData()->apCredentialsCode) {
                $currentCourse->getCustomData()->apCredentialsCode = $newCourse->getCustomData()->apCredentialsCode;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apCredentialsType !== $newCourse->getCustomData()->apCredentialsType) {
                $currentCourse->getCustomData()->apCredentialsType = $newCourse->getCustomData()->apCredentialsType;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apCredentialsGuid !== $newCourse->getCustomData()->apCredentialsGuid) {
                $currentCourse->getCustomData()->apCredentialsGuid = $newCourse->getCustomData()->apCredentialsGuid;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apDisciplines !== $newCourse->getCustomData()->apDisciplines) {
                $currentCourse->getCustomData()->apDisciplines = $newCourse->getCustomData()->apDisciplines;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apDisciplines !== $newCourse->getCustomData()->apDisciplines) {
                $currentCourse->getCustomData()->apDisciplines = $newCourse->getCustomData()->apDisciplines;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apDisciplinesGuids !== $newCourse->getCustomData()->apDisciplinesGuids) {
                $currentCourse->getCustomData()->apDisciplinesGuids = $newCourse->getCustomData()->apDisciplinesGuids;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apDisciplinesDepartment !== $newCourse->getCustomData()->apDisciplinesDepartment) {
                $currentCourse->getCustomData()->apDisciplinesDepartment = $newCourse->getCustomData()->apDisciplinesDepartment;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apDisciplinesJointProgram !== $newCourse->getCustomData()->apDisciplinesJointProgram) {
                $currentCourse->getCustomData()->apDisciplinesJointProgram = $newCourse->getCustomData()->apDisciplinesJointProgram;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apDisciplinesFullTitle !== $newCourse->getCustomData()->apDisciplinesFullTitle) {
                $currentCourse->getCustomData()->apDisciplinesFullTitle = $newCourse->getCustomData()->apDisciplinesFullTitle;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apOwners !== $newCourse->getCustomData()->apOwners) {
                $currentCourse->getCustomData()->apOwners = $newCourse->getCustomData()->apOwners;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apOwnersGuids !== $newCourse->getCustomData()->apOwnersGuids) {
                $currentCourse->getCustomData()->apOwnersGuids = $newCourse->getCustomData()->apOwnersGuids;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apSiteCode !== $newCourse->getCustomData()->apSiteCode) {
                $currentCourse->getCustomData()->apSiteCode = $newCourse->getCustomData()->apSiteCode;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apSiteGuid !== $newCourse->getCustomData()->apSiteGuid) {
                $currentCourse->getCustomData()->apSiteGuid = $newCourse->getCustomData()->apSiteGuid;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apStartDate !== $newCourse->getCustomData()->apStartDate) {
                $currentCourse->getCustomData()->apStartDate = $newCourse->getCustomData()->apStartDate;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apEndDate !== $newCourse->getCustomData()->apEndDate) {
                $currentCourse->getCustomData()->apEndDate = $newCourse->getCustomData()->apEndDate;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apStatus !== $newCourse->getCustomData()->apStatus) {
                $currentCourse->getCustomData()->apStatus = $newCourse->getCustomData()->apStatus;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apProgrammeLeads !== $newCourse->getCustomData()->apProgrammeLeads) {
                $currentCourse->getCustomData()->apProgrammeLeads = $newCourse->getCustomData()->apProgrammeLeads;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apCourseCoordinators !== $newCourse->getCustomData()->apCourseCoordinators) {
                $currentCourse->getCustomData()->apCourseCoordinators = $newCourse->getCustomData()->apCourseCoordinators;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apProgrammeAdministrators !== $newCourse->getCustomData()->apProgrammeAdministrators) {
                $currentCourse->getCustomData()->apProgrammeAdministrators = $newCourse->getCustomData()->apProgrammeAdministrators;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apDegreeApprenticeshipFlag !== $newCourse->getCustomData()->apDegreeApprenticeshipFlag) {
                $currentCourse->getCustomData()->apDegreeApprenticeshipFlag = $newCourse->getCustomData()->apDegreeApprenticeshipFlag;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apUmpFlag !== $newCourse->getCustomData()->apUmpFlag) {
                $currentCourse->getCustomData()->apUmpFlag = $newCourse->getCustomData()->apUmpFlag;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apFranchiseType1 !== $newCourse->getCustomData()->apFranchiseType1) {
                $currentCourse->getCustomData()->apFranchiseType1 = $newCourse->getCustomData()->apFranchiseType1;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apSandwichDisc !== $newCourse->getCustomData()->apSandwichDisc) {
                $currentCourse->getCustomData()->apSandwichDisc = $newCourse->getCustomData()->apSandwichDisc;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->apAtasDisc !== $newCourse->getCustomData()->apAtasDisc) {
                $currentCourse->getCustomData()->apAtasDisc = $newCourse->getCustomData()->apAtasDisc;
                $hasChanges = true;
            }
        }

        if($newCourse->courseType === "section") {
            if($currentCourse->getCustomData()->sectionCode !== $newCourse->getCustomData()->sectionCode) {
                $currentCourse->getCustomData()->sectionCode = $newCourse->getCustomData()->sectionCode;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionLevel !== $newCourse->getCustomData()->sectionLevel) {
                $currentCourse->getCustomData()->sectionLevel = $newCourse->getCustomData()->sectionLevel;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionLevelGuid !== $newCourse->getCustomData()->sectionLevelGuid) {
                $currentCourse->getCustomData()->sectionLevelGuid = $newCourse->getCustomData()->sectionLevelGuid;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionAcademicYear !== $newCourse->getCustomData()->sectionAcademicYear) {
                $currentCourse->getCustomData()->sectionAcademicYear = $newCourse->getCustomData()->sectionAcademicYear;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionAcademicYearGuid !== $newCourse->getCustomData()->sectionAcademicYearGuid) {
                $currentCourse->getCustomData()->sectionAcademicYearGuid = $newCourse->getCustomData()->sectionAcademicYearGuid;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionTerm !== $newCourse->getCustomData()->sectionTerm) {
                $currentCourse->getCustomData()->sectionTerm = $newCourse->getCustomData()->sectionTerm;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionTermGuid !== $newCourse->getCustomData()->sectionTermGuid) {
                $currentCourse->getCustomData()->sectionTermGuid = $newCourse->getCustomData()->sectionTermGuid;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionPTerm !== $newCourse->getCustomData()->sectionPTerm) {
                $currentCourse->getCustomData()->sectionPTerm = $newCourse->getCustomData()->sectionPTerm;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionPTermGuid !== $newCourse->getCustomData()->sectionPTermGuid) {
                $currentCourse->getCustomData()->sectionPTermGuid = $newCourse->getCustomData()->sectionPTermGuid;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionStartDate !== $newCourse->getCustomData()->sectionStartDate) {
                $currentCourse->getCustomData()->sectionStartDate = $newCourse->getCustomData()->sectionStartDate;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionEndDate !== $newCourse->getCustomData()->sectionEndDate) {
                $currentCourse->getCustomData()->sectionEndDate = $newCourse->getCustomData()->sectionEndDate;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionArchiveDate !== $newCourse->getCustomData()->sectionArchiveDate) {
                $currentCourse->getCustomData()->sectionArchiveDate = $newCourse->getCustomData()->sectionArchiveDate;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionRun !== $newCourse->getCustomData()->sectionRun) {
                $currentCourse->getCustomData()->sectionRun = $newCourse->getCustomData()->sectionRun;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionOwningInstitutionUnits !== $newCourse->getCustomData()->sectionOwningInstitutionUnits) {
                $currentCourse->getCustomData()->sectionOwningInstitutionUnits = $newCourse->getCustomData()->sectionOwningInstitutionUnits;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionOwningInstitutionUnitsGuids !== $newCourse->getCustomData()->sectionOwningInstitutionUnitsGuids) {
                $currentCourse->getCustomData()->sectionOwningInstitutionUnitsGuids = $newCourse->getCustomData()->sectionOwningInstitutionUnitsGuids;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionSiteCode !== $newCourse->getCustomData()->sectionSiteCode) {
                $currentCourse->getCustomData()->sectionSiteCode = $newCourse->getCustomData()->sectionSiteCode;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionSiteGuid !== $newCourse->getCustomData()->sectionSiteGuid) {
                $currentCourse->getCustomData()->sectionSiteGuid = $newCourse->getCustomData()->sectionSiteGuid;
                $hasChanges = true;
            }

            if($currentCourse->getCustomData()->sectionGuid !== $newCourse->getCustomData()->sectionGuid) {
                $currentCourse->getCustomData()->sectionGuid = $newCourse->getCustomData()->sectionGuid;
                $hasChanges = true;
            }
        }

        if($hasChanges) {
            return $currentCourse;
        }

        return false;
    }

    public function getCustomData(int $id) : mdl_course_custom_fields {
        $customDataRaw = $this->courseCustomFieldService->getCustomData($id);

        $customData = new mdl_course_custom_fields();
        $customData->populateObject($customDataRaw);

        return $customData;
    }
}