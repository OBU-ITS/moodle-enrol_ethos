<?php
namespace enrol_ethos\services;

use enrol_ethos\entities\deprecation\deprecated_resource;
use enrol_ethos\ethosclient\providers\base\ethos_provider;
use enrol_ethos\ethosclient\services\ethos_available_resources_service;
use enrol_ethos\helpers\core_class_finder_helper;

class deprecation_detector_service {

    private ethos_available_resources_service $availableResourcesService;

    private array $providers;

    private function __construct()
    {
        $this->availableResourcesService = ethos_available_resources_service::getInstance();
        $this->populateProviders();
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

    private function populateProviders() {
        $this->providers = array();
        core_class_finder_helper::includeFilesInFolder("ethosclient\providers");

        foreach(get_declared_classes() as $class) {
            if (!is_subclass_of($class, "enrol_ethos\\ethosclient\\providers\\base\\ethos_provider")
                || !defined($class . "::VERSION")
                || !defined($class . "::PATH")) {
                continue;
            }

            $path = constant($class . "::PATH");
            $this->providers[$path] = constant($class . "::VERSION");
        }
    }

    public function getRelevantAvailableResources() : array {
        $allResourcesList = $this->availableResourcesService->getAvailableResources();
        $relevantResourcesList = array();

        foreach ($allResourcesList as $resource){
            if(!array_key_exists($resource->name, $this->providers)){
                continue;
            }

            $relevantResourcesList[] = $resource;
        }

        return $relevantResourcesList;
    }

    /**
     * @param array $relevantResources
     * @return deprecated_resource[]
     */
    public function getDeprecatedResources(array $relevantResources) : array {
        $deprecatedResourcesList = array();
        foreach ($relevantResources as $relevantResource) {
            $providerVersion =  trim($this->providers[$relevantResource->name], "v");
            $highestRepresentationVersion = $providerVersion;
            foreach($relevantResource->representations as $representation) {
                if(!property_exists($representation, "version")) {
                    continue;
                }

                $representationVersion = trim($representation->version , "v");

                if (version_compare($providerVersion, $representationVersion) == 0 && property_exists($representation, "deprecationNotice")){
                    $item = new deprecated_resource();
                    $item->sunsetOn = $representation->deprecationNotice->sunsetOn;
                    $item->deprecatedOn = $representation->deprecationNotice->deprecatedOn;
                    $item->description = $representation->deprecationNotice->description;
                    $deprecatedResourcesList[$relevantResource->name] = $item;
                    continue;
                }

                if (version_compare($representationVersion, $highestRepresentationVersion) == 1){
                    $highestRepresentationVersion = $representationVersion;
                }
            }
            if (version_compare($highestRepresentationVersion, $providerVersion) == 1){
                $item = array_key_exists($relevantResource->name, $deprecatedResourcesList)
                    ? $deprecatedResourcesList[$relevantResource->name]
                    : new deprecated_resource();
                $item->newVersionAvailable = $highestRepresentationVersion;
                $item->currentVersion = $providerVersion;
                $deprecatedResourcesList[$relevantResource->name] = $item;
            }
        }
        return $deprecatedResourcesList;
    }
}
