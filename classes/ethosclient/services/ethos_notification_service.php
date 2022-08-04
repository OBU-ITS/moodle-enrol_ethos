<?php
namespace enrol_ethos\ethosclient\services;

use enrol_ethos\ethosclient\client\ethos_client;
use enrol_ethos\ethosclient\consumers\base\ethos_consumer;
use enrol_ethos\ethosclient\general\class_finder;
use Exception;

class ethos_notification_service
{
    private ethos_client $ethosClient;

    /**
     * @var ethos_consumer[]
     */
    private array $consumers;

    private function __construct()
    {
        $this->ethosClient = ethos_client::getInstance();
        $this->populateConsumers();
    }

    private static ?ethos_notification_service $instance = null;
    public static function getInstance() : ethos_notification_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function populateConsumers() {

        $this->consumers = array();
        class_finder::includeAllFilesEthosClient("consumers");

        foreach(get_declared_classes() as $class) {
            $interfaces = class_implements($class);

            if (!isset($interfaces['enrol_ethos\ethosclient\consumers\base\ethos_consumer'])) {
                continue;
            }

            $instance = new $class();
            if ($instance instanceof ethos_consumer) {
                $resourceName = $instance->getResourceName();
                $this->consumers[$resourceName] = $instance;
            }
        }
    }

    /**
     * @throws Exception
     */
    public function consumeMessages($lastProcessedId = 0, $consumeLimit = 250) {
        $url = ethos_client::API_URL . "/consume?limit=". $consumeLimit ."&lastProcessedID=" . $lastProcessedId;
        return $this->ethosClient->getJson($url, null);
    }

    public function echoNames() {
        foreach($this->consumers as $consumer) {
            echo $consumer->getResourceName() . "<br />";
        }
    }
}
