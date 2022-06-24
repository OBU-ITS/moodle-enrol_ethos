<?php
namespace enrol_ethos\ethosclient\service;

use enrol_ethos\ethosclient\client\ethos_client;

class ethos_consume_service {
    private ethos_client $ethosClient;

    private function __construct()
    {
        $this->ethosClient = ethos_client::getInstance();
    }

    private static ?ethos_consume_service $instance = null;
    public static function getInstance() : ethos_consume_service
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