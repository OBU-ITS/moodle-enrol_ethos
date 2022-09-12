<?php
namespace enrol_ethos\services\sync;

use enrol_ethos\ethosclient\providers\ethos_person_hold_provider;
use progress_trace;

class obu_sync_person_hold_service
{
    private ethos_person_hold_provider $personHoldProvider;

    private function __construct()
    {
        $this->personHoldProvider = ethos_person_hold_provider::getInstance();
    }

    private static ?obu_sync_person_hold_service $instance = null;
    public static function getInstance(): obu_sync_person_hold_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function sync(progress_trace $trace, string $id) {
        $hold = $this->personHoldProvider->get($id);
        if($hold == null) {
            $trace->output("Hold ($id) not found to update.");
            return;
        }

        // get the Moodle user record

        // update the person hold profile field.
    }

    public function remove(progress_trace $trace, string $id) {
        // find user where profile field contains $id

        // remove hold from person
    }
}