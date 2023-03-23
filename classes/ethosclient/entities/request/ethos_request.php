<?php
namespace enrol_ethos\ethosclient\entities\request;

class ethos_request {
    public string $id;
    public string $requested;
    public string $request_duration;
    public string $status;
    public string $received_count;
    public string $remaining_count;

    public function __construct() {
    }

    public function populateObject(object $data) {
        if(!isset($data)) {
            return;
        }

        $this->id = $data->id;
        $this->requested = $data->requested;
        $this->request_duration = $data->request_duration;
        $this->status = $data->status;
        $this->received_count = $data->received_count;
        $this->remaining_count = $data->remaining_count;
    }
}