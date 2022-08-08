<?php
namespace enrol_ethos\ethosclient\entities\consume;

class ethos_notification {
    public string $id;
    public string $published;
    public string $resourceName;
    public string $resourceId;
    public string $operation;

    public function __construct(object $data) {
        $this->populateObject($data);
    }

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }
        if(!isset($data->resource)) {
            $this->resourceName = $data->resource->name;
            $this->resourceId = $data->resource->id;
        }

        $this->id = $data->id;
        $this->published = $data->published;
        $this->operation = $data->operation;
    }
}