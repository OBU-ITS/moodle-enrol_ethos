<?php
namespace enrol_ethos\services;
use enrol_ethos\interfaces\course_repository_interface;
use enrol_ethos\interfaces\course_category_repository_interface;
use enrol_ethos\entities\course;

class course_service {

    private $courseRepository;
    private $courseCategoryRepository;

    public function __construct(course_repository_interface $courseRepository, course_category_repository_interface $courseCategoryRepository = null) {
        $this->courseRepository = $courseRepository;
        $this->courseCategoryRepository = $courseCategoryRepository;
    }

    public function getCourseById($courseId) {
        return $this->courseRepository->findOne($courseId);
    }

    public function getCourseByShortName($shortName) {
        return $this->courseRepository->findOneByShortName($shortName);
    }
        
    public function updateOrCreateCourse($data) {

        if (!$this->courseCategoryRepository) {
            throw Exception("No course category repo set");
        }

        $categories = $data['categories'];
        $parentCategory = get_config('enrol_ethos', 'catselect') ?: 1;
        $currentCategory = $parentCategory;

        $numCategories = count($categories);

        for ($i = 0; $i < $numCategories; $i++) {
            if ($categories[$i]) {
                if ($category = $this->courseCategoryRepository->findOneByNameAndParent($categories[$i], $currentCategory)) {
                    
                    $currentCategory = $category->id;

                    //If last category in the array, we'll use this for the course
                    if ($i === ($numCategories-1)) {
                        $parentCategory = $category->id;
                    }
                } else {
                    break;
                }
            }
        }

        $course = $this->getCourseById($data['idnumber']);

        if (!$course) {
            
            $course = new course(
                $data['idnumber'],
                $data['shortname'],
                $data['name'],
                $parentCategory
            );

            $course = $this->courseRepository->create($course);

            return $course;

        } else {

            $course->shortname = $data['shortname'];
            $course->name = $data['name'];
            $course->category = $parentCategory;

            $this->courseRepository->update($course);

            return $course;

        }
    }
}