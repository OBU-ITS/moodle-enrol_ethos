<?php
namespace enrol_ethos\ethosclient\service;

class ethos_person_service extends ethos_service
{
    private ethos_alternative_credential_service $alternativeCredentialService;

    public function __construct()
    {
        parent::__construct();
        $this->prepareService('persons', 'v12');
        $this->alternativeCredentialService = ethos_alternative_credential_service::getInstance();
    }

    private static ?ethos_person_service $instance = null;
    public static function getInstance() : ethos_person_service
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