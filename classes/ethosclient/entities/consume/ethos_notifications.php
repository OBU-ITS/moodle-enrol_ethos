<?php
namespace enrol_ethos\ethosclient\entities\consume;

class ethos_notifications {

    public function __construct()
    {
        $this->notifications = array();
    }

    /**
     * @var ethos_notification[][]
     */
    private array $notifications;

    /**
     * @return string[]
     */
    public function getNotificationGroupKeys() : array {
        return array_keys($this->notifications);
    }

    /**
     * @param string $resourceName
     * @return ethos_notification[]
     */
    public function getNotificationsByResource(string $resourceName) : array {
        if(array_key_exists($resourceName, $this->notifications)) {
            return $this->notifications[$resourceName];
        }

        return array();
    }

    /**
     * @param ethos_notification $notification
     */
    public function addNotification(ethos_notification $notification) {
        if(!array_key_exists($notification->resourceName, $this->notifications)) {
            $this->notifications[$notification->resourceName] = array();
        }

        if(!array_key_exists($notification->resourceId, $this->notifications[$notification->resourceName])) {
            $this->notifications[$notification->resourceName][$notification->operation . "_" . $notification->resourceId] = $notification;
        }
    }
}