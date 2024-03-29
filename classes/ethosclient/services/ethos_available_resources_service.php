<?php
namespace enrol_ethos\ethosclient\services;

use enrol_ethos\ethosclient\client\ethos_client;

class ethos_available_resources_service {
    private ethos_client $ethosClient;

    private function __construct()
    {
        $this->ethosClient = ethos_client::getInstance();
    }

    private static ?ethos_available_resources_service $instance = null;
    public static function getInstance() : ethos_available_resources_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getAvailableResources() : array {
        $url = ethos_client::API_URL . "/admin/available-resources";
        $availableResources = $this->ethosClient->getJson($url, '')->messages;
        $resourcesList = array();

        foreach ($availableResources as $availableResource){
            if(!property_exists($availableResource, "about")
                || ($availableResource->about[0]->name != "StudentApi"
                    && $availableResource->about[0]->name != "IntegrationApi")){
                continue;
            }
            $resourcesList = $availableResource->resources;
        }

        return $resourcesList;
    }
}