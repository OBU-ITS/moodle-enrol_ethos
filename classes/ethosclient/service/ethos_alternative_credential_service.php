<?php
namespace enrol_ethos\ethosclient\service;

use enrol_ethos\ethosclient\service\cache\cache_service;

class ethos_alternative_credential_service extends ethos_service
{
    const EMPLOYEE_CRED_CACHE_KEY = "EmployeeNumberAlternativeCredentialType";
    const EMPLOYEE_CRED_BANNER_CODE = "EMPN";

    private function __construct()
    {
        parent::__construct();
        $this->prepareService('alternative-credential-types', 'v1');
    }

    private static ?ethos_alternative_credential_service $instance = null;
    public static function getInstance() : ethos_alternative_credential_service
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