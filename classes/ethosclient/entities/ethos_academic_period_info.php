<?php

namespace enrol_ethos\ethosclient\entities;

use enrol_ethos\ethosclient\services\ethos_academic_period_category_service;

class ethos_academic_period_info
{
    public function __construct(object $data)
    {
        $this->populateObject($data);
    }

    public string $id;
    public string $code;
    public string $startOn;
    public string $endOn;
    public string $title;

    public ethos_academic_period_category_info $category;

    private function setCategory($categoryObj)
    {
        $service = ethos_academic_period_category_service::getInstance();
        $this->category = $service->get($categoryObj);
    }

    private function populateObject($data){
        $this->id = $data->id;
        $this->code = $data->code;
        $this->startOn = $data->startOn;
        $this->endOn = $data->endOn;
        $this->title = $data->title;
        $this->setCategory($data->category);
    }
}
