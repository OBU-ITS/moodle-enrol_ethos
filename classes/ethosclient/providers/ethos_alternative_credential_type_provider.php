<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_alternative_credential_type_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;
use enrol_ethos\ethosclient\services\cache\cache_service;

class ethos_alternative_credential_type_provider extends ethos_provider
{
    const EMPLOYEE_CRED_CACHE_KEY = "EmployeeNumberAlternativeCredentialType";
    const EMPLOYEE_CRED_BANNER_CODE = "EMPN";
    const VERSION = 'v1';
    const PATH = 'alternative-credential-types';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
    }

    private static ?ethos_alternative_credential_type_provider $instance = null;
    public static function getInstance() : ethos_alternative_credential_type_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($id) : ?ethos_alternative_credential_type_info {
        $item = $this->getFromEthosById($id);

        return $this->convert($item);
    }

    public function getEmployeeNumberAlternativeCredentialType() : ?ethos_alternative_credential_type_info {

        if ($cacheValue = $this->cacheService->getFromCache(self::EMPLOYEE_CRED_CACHE_KEY, cache_service::DEFAULT_COLLECTION)) {
            return $this->convert($cacheValue);
        }

        $alternativeCredentialTypes = $this->getAll();

        foreach ($alternativeCredentialTypes as $alternativeCredentialType ) {
            if ($alternativeCredentialType->code == self::EMPLOYEE_CRED_BANNER_CODE) {
                $this->cacheService->addToCacheExpanded(self::EMPLOYEE_CRED_CACHE_KEY, $alternativeCredentialType, cache_service::DEFAULT_COLLECTION, 3600);
                return $this->convert($alternativeCredentialType);
            }
        }

        return null;
    }

    /**
     * @return ethos_alternative_credential_type_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_alternative_credential_type_info {
        return new ethos_alternative_credential_type_info($item);
    }
}