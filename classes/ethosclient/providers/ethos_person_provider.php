<?php
namespace enrol_ethos\ethosclient\providers;

use enrol_ethos\ethosclient\entities\ethos_person_info;
use enrol_ethos\ethosclient\providers\base\ethos_provider;

class ethos_person_provider extends ethos_provider
{
    private ethos_alternative_credential_type_provider $alternativeCredentialService;
    const VERSION = 'v12';
    const PATH = 'persons';

    private function __construct()
    {
        parent::__construct();
        $this->prepareProvider(self::PATH, self::VERSION);
        $this->alternativeCredentialService = ethos_alternative_credential_type_provider::getInstance();
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

    public function get($id) : ?ethos_person_info {
        $person = $this->getFromEthosById($id);

        if(!$person || isset($person->errors)) {
            return null;
        }

        return $this->convert($person);
    }

    /**
     * @param $bannerId
     * @return ethos_person_info[]
     */
    public function getPersonsByBannerId($bannerId) : array {
        $url = $this->buildUrlWithCriteria('{"credentials":[{"type":"bannerId","value":"' . $bannerId . '}]}');
        $items = $this->getFromEthos($url);
        return array_map(array($this, 'convert'), $items);
    }

    /**
     * @param $credential
     * @return ethos_person_info[]
     */
    public function getPersonByEmployeeAlternativeCredential($credential) : array{
        $credentialType = $this->alternativeCredentialService->getEmployeeNumberAlternativeCredentialType();

        $url = $this->buildUrlWithCriteria('{"alternativeCredentials":[{"type":{"id":"' . $credentialType->id . '"},"value":"' . $credential . '"}]}');



        $items = $this->getFromEthos($url);
        return array_map(array($this, 'convert'), $items);
    }

    /**
     * @return ethos_person_info[]
     */
    public function getPersonsWithStudentRole() : array {
        $url = $this->buildUrlWithCriteria('{"roles":[{"role":"student"}]}');
        $items = $this->getFromEthos($url, true);
        return array_map(array($this, 'convert'), $items);
    }


    /**
     * @param $limit
     * @param $offset
     * @return ethos_person_info[]
     */
    public function getBatch($limit, $offset) : array {
        $items = $this->getFromEthos(null, true, $limit, $offset);

        return array_map(array($this, 'convert'), $items);
    }

    /**
     * @return ethos_person_info[]
     */
    public function getAll() : array {
        $items = $this->getFromEthos();

        return array_map(array($this, 'convert'), $items);
    }

    private function convert(object $item) : ?ethos_person_info {
        return new ethos_person_info($item);
    }
}