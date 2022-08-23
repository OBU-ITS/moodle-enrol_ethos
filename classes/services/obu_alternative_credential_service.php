<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\entities\ethos_alternative_credential_type_info;
use enrol_ethos\ethosclient\providers\ethos_alternative_credential_type_provider;

class obu_alternative_credential_service
{
    private ethos_alternative_credential_type_provider $alternativeCredentialService;

    private function __construct()
    {
        $this->alternativeCredentialService = ethos_alternative_credential_type_provider::getInstance();
    }

    private static ?obu_alternative_credential_service $instance = null;
    public static function getInstance(): obu_alternative_credential_service
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getEmployeeNumberAlternativeCredentialType() : ethos_alternative_credential_type_info {
        return $this->alternativeCredentialService->getEmployeeNumberAlternativeCredentialType();
    }

    public function getAlternativeCredentialOfType($peron, $alternativeCredentialType) : string {
        if(!isset($peron) || !isset($peron->alternativeCredentials) || !isset($alternativeCredentialType) || !isset($alternativeCredentialType->id)) {
            return '';
        }

        foreach ($peron->alternativeCredentials as $alternativeCredential) {
            if(!isset($alternativeCredential->type)
                || !isset($alternativeCredential->type->id)
                || $alternativeCredential->type->id != $alternativeCredentialType->id) {
                continue;
            }
            return $alternativeCredential->value;
        }

        return '';
    }

    public function hasAlternativeCredentialOfType($peron, $alternativeCredentialType) : bool {
        $value = $this->getAlternativeCredentialOfType($peron, $alternativeCredentialType);

        return $value != '';
    }
}