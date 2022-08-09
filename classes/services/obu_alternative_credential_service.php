<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\providers\ethos_alternative_credential_type_provider;

class obu_alternative_credential_service
{
    private ethos_alternative_credential_type_provider $alternativeCredentialService;

    public function __construct()
    {
        $this->alternativeCredentialService = ethos_alternative_credential_type_provider::getInstance();
    }

    public function getEmployeeNumberAlternativeCredentialType() : ?object {
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