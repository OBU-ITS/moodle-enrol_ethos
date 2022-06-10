<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\client\ethos_client;

class alternative_credential_service
{
    private ethos_client $ethosClient;

    public function __construct()
    {
        $this->ethosClient = new ethos_client();
    }

    public function getEmployeeNumberAlternativeCredentialType() {
        return $this->ethosClient->getEmployeeNumberAlternativeCredentialType();
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