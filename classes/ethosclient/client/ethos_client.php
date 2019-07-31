<?php

namespace enrol_ethos\ethosclient\client;

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class ethos_client
{
    private $client = null;
    const API_URL = "https://integrate.elluciancloud.ie";

    public $authKey;
    public $accessToken;

    private $studentTypeArray = array();
    private $academicProgramArray = array();
    private $institutionArray = array();
    private $academicLevelArray = array();
    private $academicPeriodArray = array();
    private $siteArray = array();
    private $enrollmentStatusArray = array();
    private $academicCredentialArray = array();
    private $studentStatusArray = array();
    private $academicDisciplineArray = array();

    public function __construct($key)
    {
        $this->authKey = $key;
        $this->client = new Client();
    }

    public function getJson($url, $accept, $maxResults = 0, $resultsPerPage = 0){

        // Do we need to page the results?
        
        // 0 0 dont page
        // 500 500 dont page
        // 400 500 dont page
        // 0 500 page 

        if ((!$resultsPerPage) || ($maxResults && ($maxResults <= $resultsPerPage))) {
            return json_decode($this->get($url, $accept));
        }

        $maxResults = $maxResults ? $maxResults : 100000;

        $results = array();

        for ($offset = 0; $offset < $maxResults; $offset+=$resultsPerPage) {
            $url1 = "$url?limit=$resultsPerPage&offset=$offset";
            $jsonResult = json_decode($this->get($url1, $accept));
            $results = array_merge($jsonResult,$results);
            
            if (count($jsonResult) < $resultsPerPage) {
                break;
            }
        }

        return $results;
    }

    public function get($url, $accept) {
        
        $maxTries = 3;
        $tries = 0;

        while ($tries < $maxTries) {

            // if ($tries == 0 && $this->accessToken) {
            //     // Simulate failure on first access after successful auth.
            //     $this->accessToken = 'X';
            // }

            try {
                $options = [
                    'headers' => [
                        'Content-Type'      => 'application/json',
                        'Accept-Charset'    => 'UTF-8',
                        'Accept'            => $accept,
                        'Authorization'     => 'Bearer ' . $this->getAccessToken()
                    ],
                ];
        
                if (isset($GLOBALS['debug-alluser-issue'])) {
                    var_dump($url);
                    var_dump($options);
                }

                $response = $this->client->get($url, $options);

                if (isset($GLOBALS['debug-alluser-issue'])) var_dump($response);
                return $response->getBody()->getContents();

            } catch (RequestException $e) {
                // Handle non 200 responses - includes regenerating access token if necessary
                
                if (isset($GLOBALS['debug-alluser-issue'])) {
                    echo "EXCEPTION";
                    var_dump($e);
                }

                $response = $this->StatusCodeHandling($e);
                if (++$tries == $maxTries) {
                    // Max consecutive errors - not solved by retrying.
                    // TODO: Exponential back off.
                    throw $e;
                }
            }
        }
    }

    public function prepareAccessToken()
    {
        try {
            $url = self::API_URL . '/auth';

            $options = [
                'headers' => [
                    'Authorization'      => "Bearer {$this->authKey}",
                    'Cache-Control'      => 'no-cache'
                ],
            ];

            $response = $this->client->post($url, $options);
            $this->accessToken = $response->getBody()->getContents();
        } catch (RequestException $e) {
            //TODO: Retry logic
            throw $e;
        }
    }

    public function getAccessToken() {
        if (!$this->accessToken) {
            $this->prepareAccessToken();
        }

        return $this->accessToken;
    }

    public function StatusCodeHandling($e)
    {
        if (isset($GLOBALS['debug-alluser-issue'])) var_dump($e->getResponse()->getStatusCode());
    
        if ($e->getResponse()->getStatusCode() == '400') {
            $this->prepareAccessToken();
        } elseif ($e->getResponse()->getStatusCode() == '422') {
            $response = json_decode($e->getResponse()->getBody(true)->getContents());
            return $response;
        } elseif ($e->getResponse()->getStatusCode() == '500') {
            $response = json_decode($e->getResponse()->getBody(true)->getContents());
            return $response;
        } elseif ($e->getResponse()->getStatusCode() == '401') {
            $this->prepareAccessToken();
        } elseif ($e->getResponse()->getStatusCode() == '403') {
            $response = json_decode($e->getResponse()->getBody(true)->getContents());
            return $response;
        } else {
            $response = json_decode($e->getResponse()->getBody(true)->getContents());
            return $response;
        }
    }

    public function getPersonById($id) {
        try {
            if (!$this->accessToken) {
                $this->prepareAccessToken();
            }
            $url = self::API_URL . '/api/persons/' . $id;
            $options = [
                'headers' => [
                    'Content-Type'      => 'application/vnd.hedtech.applications.v12+json',
                    'Accept-Charset'    => 'UTF-8',
                    'Accept'            => 'application/json',
                    'Authorization'     => 'Bearer ' . $this->accessToken
                ],
            ];

            $response = $this->client->get($url, $options);
            $result = $response->getBody()->getContents();
            return json_decode($result);
        } catch (RequestException $e) {
            $response = $this->StatusCodeHandling($e);
            return $response;
        }
    }


    public function getPersonsByBannerId($bannerId) {
        if (isset($GLOBALS['debug-alluser-issue'])) unset($GLOBALS['debug-alluser-issue']);
        
        if ($bannerId == '17047217') {
            $GLOBALS['debug-alluser-issue'] = true;
        }
        
        $url = self::API_URL . "/api/persons?criteria={\"credentials\":[{\"type\":\"bannerId\",\"value\":\"" . $bannerId . "\"}]}";        
        $accept = 'application/vnd.hedtech.integration.v12.1.0+json';
        return $this->getJson($url, $accept);
    }

    public function getStudentById($id) {
        /**
         * Get a single student
         */
        $url = self::API_URL . '/api/students/' . $id;
        $accept = 'application/vnd.hedtech.integration.v7+json';
        return $this->getJson($url, $accept);
    }

    public function getStudentByPersonId($personId) {
        /**
         * Get a single student
         */
        $url = self::API_URL . '/api/students?person=' . $personId;
        $accept = 'application/vnd.hedtech.integration.v7+json';
        return $this->getJson($url, $accept);
    }

    public function getStudentType($id) {
        if (!array_key_exists($id, $this->studentTypeArray)) {
            $url = self::API_URL . "/api/student-types/" . $id;
            $accept = 'application/vnd.hedtech.integration.v7+json';

            $this->studentTypeArray[$id] = $this->getJson($url, $accept);
        }

        return $this->studentTypeArray[$id];
    }

    public function getStudents()
    {
        $url = self::API_URL . "/api/students";
        $accept = 'application/vnd->hedtech->applications->v7+json';
        return $this->getJson($url, $accept);
    }


    // public function getStudentAcademicProgram($academicProgramId) {

    //     try {
    //         if (!$this->accessToken) {
    //             $this->prepareAccessToken();
    //         }
    //         $url = self::API_URL -> "/api/students";


    //     }

    //     var url = ethosAPIProperties->getScheme() + "://" + ethosAPIProperties->getHost() + "/api/student-academic-programs/" + academicProgramId

    //     val headers = getHeaders()
    //     val request = HttpEntity("", headers)
    //     logger->debug("Getting from " + url)
    //     val respType = object: ParameterizedTypeReference<StudentAcademicProgram>(){}
    //     var responseEntity = restTemplate->exchange(url, HttpMethod->GET, request, respType)

    //     return responseEntity->body!!
    // }

    public function getStudentAcademicProgramsByPersonId($personId) {
        $url = self::API_URL . "/api/student-academic-programs?student=" . $personId;
        $accept = 'application/vnd.hedtech.integration.v7+json';
        return $this->getJson($url, $accept);
    }


    public function getAcademicProgram($id) {

        if (!array_key_exists($id, $this->academicProgramArray)) {
            $url = self::API_URL . "/api/academic-programs/" . $id;
            $accept = 'application/vnd.hedtech.integration.v7+json';
            $this->academicProgramArray[$id] = $this->getJson($url, $accept);
        }

        return $this->academicProgramArray[$id];
    }

    public function getAcademicPrograms() {
        $url = self::API_URL . "/api/academic-programs";
        $accept = 'application/vnd.hedtech.integration.v7+json';
        $result = $this->getJson($url, $accept, 0, 500);
    
        return $result;
    }


    public function getInstitution($id) {

        if (!array_key_exists($id, $this->institutionArray)) {
            $url = self::API_URL . "/api/educational-institution-units/" . $id;
            $accept = 'application/vnd.hedtech.integration.v7+json';
            $this->institutionArray[$id] = $this->getJson($url, $accept);
        }

        return $this->institutionArray[$id];
    }

    public function getAcademicLevel($id) {
        if (!array_key_exists($id, $this->academicLevelArray)) {
            $url = self::API_URL . "/api/academic-levels/" . $id;
            $accept = 'application/vnd.hedtech.integration.v6+json';
            $this->academicLevelArray[$id] = $this->getJson($url, $accept);
        }

        return $this->academicLevelArray[$id];

    }

    public function getAcademicPeriod($id) {
        if (!array_key_exists($id, $this->academicPeriodArray)) {
            $url = self::API_URL . "/api/academic-periods/" . $id;
            $accept = 'application/vnd.hedtech.integration.v8+json';
            $this->academicPeriodArray[$id] = $this->getJson($url, $accept);
        }

        return $this->academicPeriodArray[$id];

    }

    public function getSite($id) {
        if (!array_key_exists($id, $this->siteArray)) {
            $url = self::API_URL . "/api/sites/" . $id;
            $accept = 'application/vnd.hedtech.integration.v6+json';
            $this->siteArray[$id] = $this->getJson($url, $accept);
        }

        return $this->siteArray[$id];

    }

    public function getAcademicPeriodProfiles($personId, $academicPeriodId) {
        $url = self::API_URL . "/api/student-academic-period-profiles?person=" . $personId . "&academicPeriod=" . $academicPeriodId;
        $accept = 'application/vnd.hedtech.integration.v7+json';
        return $this->getJson($url, $accept);
    }

    public function getEnrollmentStatus($id) {
        if (!array_key_exists($id, $this->enrollmentStatusArray)) {
            $url = self::API_URL . "/api/academic-period-enrollment-statuses/" . $id;
            $accept = 'application/vnd.hedtech.integration.v7+json';
            $this->enrollmentStatusArray[$id] = $this->getJson($url, $accept);
        }

        return $this->enrollmentStatusArray[$id];
    }


    public function getAcademicCredential($id) {
        if (!array_key_exists($id, $this->academicCredentialArray)) {
            $url = self::API_URL . "/api/academic-credentials/" . $id;
            $accept = 'application/vnd.hedtech.integration.v6+json';
            $this->academicCredentialArray[$id] = $this->getJson($url, $accept);
        }

        return $this->academicCredentialArray[$id];
    }


    public function getStudentStatus($id) {
        if (!array_key_exists($id, $this->studentStatusArray)) {
            $url = self::API_URL . "/api/student-statuses/" . $id;
            $accept = 'application/vnd.hedtech.integration.v7+json';
            $this->studentStatusArray[$id] = $this->getJson($url, $accept);
        }

        return $this->studentStatusArray[$id];
    }


    public function getAcademicDiscipline($id) {
        if (!array_key_exists($id, $this->academicDisciplineArray)) {
            $url = self::API_URL . "/api/academic-disciplines/" . $id;
            $accept = 'application/vnd.hedtech.integration.v10+json';
            $this->academicDisciplineArray[$id] = $this->getJson($url, $accept);
        }

        return $this->academicDisciplineArray[$id];
    }


    public function consumeMessages(){
        $url = self::API_URL . "/consume?limit=200";
        return $this->getJson($url, null);
    }

}