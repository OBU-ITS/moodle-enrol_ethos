<?php
namespace enrol_ethos\services\moodle;

use enrol_ethos\entities\mdl_profile_category;
use enrol_ethos\entities\mdl_profile_field;
use enrol_ethos\repositories\db_user_profile_category_repository;
use enrol_ethos\repositories\db_user_profile_field_repository;

class mdl_user_profile_field_service
{
    private db_user_profile_category_repository $userProfileCategoryRepo;
    private db_user_profile_field_repository $userProfileFieldRepo;

    private function __construct()
    {
        $this->userProfileCategoryRepo = db_user_profile_category_repository::getInstance();
        $this->userProfileFieldRepo = db_user_profile_field_repository::getInstance();
    }

    private static ?mdl_user_profile_field_service $instance = null;
    public static function getInstance(): mdl_user_profile_field_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function ensureCustomFieldCategory($name) : mdl_profile_category {
        $existingCategory =  $this->userProfileCategoryRepo->getByName($name);
        if($existingCategory) {
            return $existingCategory;
        }

        return $this->userProfileCategoryRepo->create($name);
    }

    public function ensureCustomField(
        mdl_profile_category $category,
        string $name,
        string $shortname,
        string $type,
        int $displaySize,
        int $maxLength,
        int $visibility) {
        $fields = $this->userProfileFieldRepo->getByCategoryId($category->id);
        foreach($fields as $field) {
            if($field->shortname == $shortname) {
                return;
            }
        }

        // Defaults for other values.
        $data = [
            'categoryid' => $category->id,
            'datatype' => $type,
            'id' => 0,
            'name' => $name,
            'shortname' => $shortname,
            'description' => '',
            'descriptionformat' => 0,
            'required' => 0,
            'locked' => 1,
            'visible' => $visibility,
            'forceunique' => 0,
            'signup' => 0,
            'defaultdata' => '',
            'defaultdataformat' => 0,
            'param1' => '',
            'param2' => '',
            'param3' => '',
            'param4' => '',
            'param5' => ''
        ];

        // Type-specific defaults for other values.
        $typeDefaults = [
            'text' => [
                'param1' => $displaySize,
                'param2' => $maxLength
            ],
            'menu' => [
                'param1' => "Yes\nNo",
                'defaultdata' => 'No'
            ],
            'datetime' => [
                'param1' => '1900',
                'param2' => '2100',
                'param3' => 1
            ],
            'checkbox' => [
                'defaultdata' => 0
            ]
        ];

        foreach ($typeDefaults[$type] ?? [] as $field => $value) {
            $data[$field] = $value;
        }

        $item = new mdl_profile_field();
        $item->populateObjectByArray($data);

        $this->userProfileFieldRepo->save($item);
    }
}
