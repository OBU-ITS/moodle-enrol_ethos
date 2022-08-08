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

    public function consumeMessages($lastProcessedId = 0, $consumeLimit = 200) {
        $url = ethos_client::API_URL . "/consume?limit=". $consumeLimit ."&lastProcessedID=" . $lastProcessedId;
        return $this->ethosClient->getJson($url, null);
    }
}