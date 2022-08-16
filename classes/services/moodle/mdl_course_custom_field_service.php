<?php
namespace enrol_ethos\services\moodle;

use core_course\customfield\course_handler;
use core_customfield\category_controller;
use core_customfield\field_controller;

class mdl_course_custom_field_service
{
    private course_handler $handler;

    private function __construct()
    {
        $this->handler = course_handler::create();
    }

    private static ?mdl_course_custom_field_service $instance = null;
    public static function getInstance(): mdl_course_custom_field_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function ensureCustomFieldCategory($name) : category_controller {
        $categories = $this->handler->get_categories_with_fields();
        foreach($categories as $category) {
            if($category->get('name') == $name) {
                return $category;
            }
        }

        $id = $this->handler->create_category($name);

        return category_controller::create($id);
    }

    public function ensureCustomField(
        category_controller $category,
        string $name,
        string $shortname,
        string $type,
        int $displaySize,
        int $maxLength,
        int $visibility) {
        $fields = $category->get_fields();
        foreach($fields as $field) {
            if($field->get("shortname") == $shortname) {
                return;
            }
        }

        $record = new \stdClass();
        $record->name = $name;
        $record->shortname = $shortname;
        $record->type = $type;
        $record->categoryid = $category->get("id");
        switch ($type) {
            case "text":
                $data = json_encode([
                    "required" => "0",
                    "uniquevalues" => "0",
                    "defaultvalue" => "",
                    "displaysize" => $displaySize,
                    "maxlength" => $maxLength,
                    "ispassword" => "0",
                    "link" => "",
                    "locked" => "1",
                    "visibility" => "$visibility"]);
                break;
            case "date":
                $data = json_encode([
                    "required" => "0",
                    "uniquevalues" => "0",
                    "includetime" => "0",
                    "mindate" => "0",
                    "maxdate" => "0",
                    "locked" => "1",
                    "visibility" => "$visibility"]);
                break;
            default:
                $data = "";
                break;
        }

        $record->configdata = $data;

        $field = field_controller::create(0, $record, $category);

        $field->save();
    }

    public function getCustomData(int $id) : array {
        return $this->handler->get_instance_data($id, true);
    }
}