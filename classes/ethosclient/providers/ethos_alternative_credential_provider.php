<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\providers\base\ethos_provider;
use enrol_ethos\ethosclient\services\cache\cache_service;

class ethos_alternative_credential_provider extends ethos_provider
{
    const EMPLOYEE_CRED_CACHE_KEY = "EmployeeNumberAlternativeCredentialType";
    const EMPLOYEE_CRED_BANNER_CODE = "EMPN";

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('alternative-credential-types', 'v1');
    }

    private static ?ethos_alternative_credential_provider $instance = null;
    public static function getInstance() : ethos_alternative_credential_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getEmployeeNumberAlternativeCredentialType() : ?object {

        if ($cacheValue = $this->cacheService->getFromCache(self::EMPLOYEE_CRED_CACHE_KEY, cache_service::DEFAULT_COLLECTION)) {
            return $cacheValue;
        }

        $alternativeCredentialTypes = $this->getAlternativeCredentialTypes();

        foreach ($alternativeCredentialTypes as $alternativeCredentialType ) {
            if ($alternativeCredentialType->code == self::EMPLOYEE_CRED_BANNER_CODE) {
                $this->cacheService->addToCacheExpanded(self::EMPLOYEE_CRED_CACHE_KEY, $alternativeCredentialType, cache_service::DEFAULT_COLLECTION, 3600);
                return $alternativeCredentialType;
            }
        }

        return null;
    }

    public function getAlternativeCredentialTypes() : array {
        return $this->getFromEthos();
    }
}