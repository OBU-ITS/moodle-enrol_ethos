<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\services\ethos_available_resources_service;
use enrol_ethos\helpers\core_class_finder_helper;
use enrol_ethos\ethosclient\providers\base\ethos_provider;
use progress_trace;

class deprecation_detector_service {

    private ethos_available_resources_service $availableResourcesService;

    private progress_trace $trace;

    public array $providers;

    private function __construct($trace)
    {
        $this->availableResourcesService = ethos_available_resources_service::getInstance();
        $this->populateProviders();
        $this->getRelevantAvailableResources($this->providers);
        $this->trace = $trace;
    }

    private static ?deprecation_detector_service $instance = null;
    public static function getInstance() : deprecation_detector_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function populateProviders(){
        $this->providers = array();
        core_class_finder_helper::includeFilesInFolder("providers");

        foreach(get_declared_classes() as $class) {
            $interfaces = class_implements($class);

            if (!isset($interfaces['enrol_ethos\ethosclient\providers\base\ethos_provider']) || !defined($class::RESOURCE_NAME)) {
                continue;
            }

            $instance = new $class($this->trace);
            if ($instance instanceof ethos_provider) {
                $resourceName = constant($class::RESOURCE_NAME);
                $this->providers[$resourceName] = $instance;
            }
        }
    }

    public function getRelevantAvailableResources($providers){
        $allResourcesList = $this->availableResourcesService->getAvailableResources();
        $relevantResourcesList = array();
        foreach ($allResourcesList as $resource){
            foreach ($providers as $provider){
                if($resource->name === $provider->resourceName){
                    $relevantResourcesList = $resource;
                }
            }
        }
        return $relevantResourcesList;
    }
}
