<?php
namespace enrol_ethos\ethosclient\entities\consume;

class ethos_notifications {

    public function __construct()
    {
        $this->notifications = array();
        $this->notificationKeys = array();
        $this->retrievedCount = 0;
    }

    /**
     * @var int[][]
     */
    private array $notificationKeys;

    /**
     * @var ethos_notification[]
     */
    private array $notifications;

    /**
     * @return string[]
     */
    public function getNotifications() : array {
        ksort($this->notifications);
        return $this->notifications;
    }

    private int $retrievedCount;
    public function getRetrievedCount() : int {
        return $this->retrievedCount;
    }

    /**
     * @param ethos_notification $notification
     */
    public function addNotification(ethos_notification $notification) {
        $this->retrievedCount++;

        $this->ensureResourceNameGroupExists($notification->resourceName);

        $groupItemKey = $notification->operation . "_" . $notification->resourceId;
        if(!array_key_exists($groupItemKey, $this->notificationKeys[$notification->resourceName])) {
            $this->notificationKeys[$notification->resourceName][$groupItemKey] = $notification->resourceId;
            $this->notifications[$notification->id] = $notification;
        }
    }

    /**
     * @param $resourceName
     * @return void
     */
    private function ensureResourceNameGroupExists($resourceName) {
        if(!array_key_exists($resourceName, $this->notificationKeys)) {
            $this->notificationKeys[$resourceName] = array();
        }
    }
}