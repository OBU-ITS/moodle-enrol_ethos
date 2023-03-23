<?php
namespace enrol_ethos\services;

class obu_email_service {

    private function __construct()
    {
    }

    private static ?obu_email_service $instance = null;
    public static function getInstance() : obu_email_service
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function createEmailMessage($resources) {
        foreach ($resources as $resource) {
            if ($resource) {

            }
        }
    }

    public static function sendEmail() {

    }

}