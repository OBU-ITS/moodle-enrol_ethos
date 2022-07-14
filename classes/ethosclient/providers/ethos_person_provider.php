<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_person_provider extends ethos_provider
{
    private ethos_alternative_credential_provider $alternativeCredentialService;

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider('persons', 'v12');
        $this->alternativeCredentialService = ethos_alternative_credential_provider::getInstance();
    }

    private static ?ethos_person_provider $instance = null;
    public static function getInstance() : ethos_person_provider
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getPersonById($id) : ?object {
        $person = $this->getFromEthosById($id);

        if(!$person || isset($person->errors)) {
            return null;
        }

        return $person;
    }

    public function getPersonsByBannerId($bannerId) : array {
        $url = $this->buildUrlWithCriteria('{"credentials":[{"type":"bannerId","value":"' . $bannerId . '}]}');

        return $this->getFromEthos($url);
    }

    public function getPersonByEmployeeAlternativeCredential($credential) : array{
        $credentialType = $this->alternativeCredentialService->getEmployeeNumberAlternativeCredentialType();

        $url = $this->buildUrlWithCriteria('{"alternativeCredentials":[{"type":{"id":"' . $credentialType->id . '"},"value":"' . $credential . '"}]}');

        return $this->getFromEthos($url);
    }

    public function getPersonsWithStudentRole() : array {
        $url = $this->buildUrlWithCriteria('{"roles":[{"role":"student"}]}');

        return $this->getFromEthos($url, true);
    }
}