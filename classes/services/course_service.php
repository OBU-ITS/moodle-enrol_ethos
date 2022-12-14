<?php
namespace enrol_ethos\services;
use enrol_ethos\entities\mdl_course;
use enrol_ethos\repositories\db_course_category_repository;
use enrol_ethos\repositories\db_course_repository;

class course_service {

    private db_course_repository $courseRepository;
    private db_course_category_repository $courseCategoryRepository;

    public function __construct(db_course_repository $courseRepository, db_course_category_repository $courseCategoryRepository) {
        $this->courseRepository = $courseRepository;
        $this->courseCategoryRepository = $courseCategoryRepository;
    }

    public function getCourseById($courseId) {
        return $this->courseRepository->findOne($courseId);
    }

//    public function getCourseByShortName($shortName) {
//        return $this->courseRepository->findOneByShortName($shortName);
//    }
//
//    public function updateOrCreateCourse($data) {
//        $categories = $data['categories'];
//        $parentCategory = get_config('enrol_ethos', 'catselect') ?: 1;
//        $currentCategory = $parentCategory;
//
//        $numCategories = count($categories);
//
//        for ($i = 0; $i < $numCategories; $i++) {
//            if ($categories[$i]) {
//                if ($category = $this->courseCategoryRepository->getByNameAndParent($categories[$i], $currentCategory)) {
//
//                    $currentCategory = $category->id;
//
//                    //If last category in the array, we'll use this for the course
//                    if ($i === ($numCategories-1)) {
//                        $parentCategory = $category->id;
//                    }
//                } else {
//                    break;
//                }
//            }
//        }
//
//        $course = $this->getCourseById($data['idnumber']);
//
//        //Hack to migrate old course IDs
//        if (!$course && $data['oldidnumber'] && ($data['oldidnumber'] !== $data['idnumber'])) {
//            $course = $this->getCourseById($data['oldidnumber']);
//        }
//
//        if (!$course) {
//
//            $course = new mdl_course(
//                $data['idnumber'],
//                $data['shortname'],
//                $data['name'],
//                $parentCategory
//            );
//
//            $course = $this->courseRepository->create($course);
//
//        } else {
//
//            $course->idnumber = $data['idnumber'];
//            $course->shortname = $data['shortname'];
//            $course->name = $data['name'];
//            $course->category = $parentCategory;
//
//            $this->courseRepository->update($course);
//
//        }
//
//        return $course;
//    }
}