<?php
namespace enrol_ethos\entities\reports;

class report_action {
    public int $run_id;
    public string $action_type;
    public string $resource_name;
    public string $resource_id;
    public string $resource_description;

    public function __construct($action_type, $resource_name, $resource_id, $resource_description="") {
        $this->action_type = $action_type;
        $this->resource_name = $resource_name;
        $this->resource_id = $resource_id;
        $this->resource_description = $resource_description;
    }
}