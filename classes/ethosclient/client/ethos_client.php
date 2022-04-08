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

    private $cache = array();
    private $personCache = array();
    private $personBannerIdMap = array();

    private $apiMap = array(
                    'AcademicDisciplines'   => array(   'path'      => 'academic-disciplines',
                                                        'accept'    => 'application/vnd.hedtech.integration.v15+json',
                                                        'cachable'  => true ),

                    'StudentStatuses'       => array(   'path'      => 'student-statuses',
                                                        'accept'    =>  'application/vnd.hedtech.integration.v7+json',
                                                        'cachable'  => true ),

                    'AcademicCredentials'   => array(   'path'      => 'academic-credentials',
                                                        'accept'    =>  'application/vnd.hedtech.integration.v6+json',
                                                        'cachable'  => true ),

                    'EnrolmentStatuses'     => array(   'path'      => 'student-academic-period-statuses',
                                                        'accept'    =>  'application/vnd.hedtech.integration.v1+json',
                                                        'cachable'  => true ),

                    'Sites'                 => array(   'path'      => 'sites',
                                                        'accept'    =>  'application/vnd.hedtech.integration.v6+json',
                                                        'cachable'  => true ),

                    'AcademicPeriods'       => array(   'path'      =>  'academic-periods',
                                                        'accept'    =>  'application/vnd.hedtech.integration.v16+json',
                                                        'cachable'  => true ),

                    'AcademicLevels'        => array(   'path'      => 'academic-levels',
                                                        'accept'    =>  'application/vnd.hedtech.integration.v6+json',
                                                        'cachable'  => true ),

                    'Institutions'          => array(   'path'      => 'educational-institution-units',
                                                        'accept'    =>  'application/vnd.hedtech.integration.v7+json',
                                                        'cachable'  => true ),

                    'AcademicPrograms'      => array(   'path'      => 'academic-programs',
                                                        'accept'    =>  'application/vnd.hedtech.integration.v15+json',
                                                        'cachable'  => true ),
     
                    'StudentTypes'          => array(   'path'      => 'student-types',
                                                        'accept'    =>  'application/vnd.hedtech.integration.v7+json',
                                                        'cachable'  => true ),

                    'Persons'               => array(   'path'      => 'persons',
                                                        'accept'    => 'application/vnd.hedtech.integration.v12+json',
                                                        'cachable'  => false ),

                    'Students'              => array(   'path'      => 'students',
                                                        'accept'    => 'application/vnd.hedtech.integration.v16+json',
                                                        'cachable'  => false ),

                    'StudentAcademicPrograms'  => array(     'path'      => 'student-academic-programs',
                                                             'accept'    => 'application/vnd.hedtech.integration.v17+json',
                                                             'cachable'  => false ),

                    'AcademicPeriodProfiles'  => array(      'path'      => 'student-academic-periods',
                                                             'accept'    => 'application/vnd.hedtech.integration.v1+json',
                                                             'cachable'  => false ));
                                                             
                                                                                            
    public function __construct($key)
    {
        $this->authKey = $key;
        $this->client = new Client();
    }

    public function consumeMessages(){
        $url = self::API_URL . "/consume?limit=200";
        return $this->getJson($url, null);
    }

    public function cacheAllReferenceTypes() {
        foreach ($this->apiMap as $key=>$val) {
            if ($val['cachable']) $this->getByMap($key);
        }
    }

    public function cacheAllPersonRecords() {

        ini_set('memory_limit', '-1');

        $personRecords = $this->getPersonsWithStudentRole();
        //if ($drew = $this->getPersonsByBannerId('19003314',false)) array_push($personRecords,$drew[0]);
        $this->personCache = $personRecords;

        foreach ($personRecords as $person) {
            foreach ($person->credentials as $credential) {
                if ($credential->type=='bannerId') {
                    $this->personBannerIdMap[$credential->value] = $person;
                }
            }
            //$this->personBannerIdMap[]
        }
    }

    public function getByMap($resourceName, $id=null, $urlOverride=null, $paged=null, $maxResults=0) {
        if (array_key_exists($resourceName,$this->apiMap) && $resource = $this->apiMap[$resourceName]) {
            
            if ($resource['cachable'] 
                && array_key_exists($resourceName,$this->cache)
                && array_key_exists($id, $this->cache[$resourceName])) {
                return $this->cache[$resourceName][$id];
            }

            $url = $urlOverride ? $urlOverride :
                self::API_URL . "/api/{$resource['path']}";

            
            if (!$urlOverride) {
                if ($id) {
                    $url = $url . "/$id";
                    $paged = $paged === null ? false : $paged;
                } else {
                    $paged = $paged === null ? true : $paged;
                }
            }
            
            if (!$paged) {
                $result = $this->getJson($url, $resource['accept']);
            } else {
                $result = $this->getJson($url, $resource['accept'],$maxResults,500);
            }

            if ($resource['cachable']) {
                if ($id) {
                    // Cache single object using provided id
                    $this->cache[$resourceName][$id] = $result;
                } else {   
                    // Assume array and cache all the results
                    foreach ($result as $res) {
                        $this->cache[$resourceName][$res->id] = $res;
                    }
                }
            }

            return $result;
        }
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
            $qjoin = strpos($url,'?') ? '&' : '?';
            $url1 = "{$url}{$qjoin}limit=$resultsPerPage&offset=$offset";
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
        $statusCode = $e->getResponse()->getStatusCode();

        if ($statusCode == '400') {
            $this->prepareAccessToken();
        } elseif ($statusCode == '422') {
            $response = json_decode($e->getResponse()->getBody(true)->getContents());
            return $response;
        } elseif ($statusCode == '500') {
            $response = json_decode($e->getResponse()->getBody(true)->getContents());
            return $response;
        } elseif ($statusCode == '401') {
            $this->prepareAccessToken();
        } elseif ($statusCode == '403') {
            $response = json_decode($e->getResponse()->getBody(true)->getContents());
            return $response;
        } else {
            $response = json_decode($e->getResponse()->getBody(true)->getContents());
            return $response;
        }
    }

    public function getPersonById($id) {
        return $this->getByMap('Persons',$id);
    }

    public function getPersonsByBannerId($bannerId, $useCache=true) {        
        if ($useCache) {
            if (array_key_exists($bannerId, $this->personBannerIdMap)) {
                return array($this->personBannerIdMap[$bannerId]);
            }
        } else {
            $url = self::API_URL . "/api/persons?criteria={\"credentials\":[{\"type\":\"bannerId\",\"value\":\"" . $bannerId . "\"}]}";
            return $this->getByMap('Persons', null, $url);
        }

        return false;
    }

    public function getPersonsWithStudentRole() {
        $url = self::API_URL . '/api/persons?criteria={"roles":[{"role":"student"}]}';
        return $this->getByMap('Persons',null,$url,true);
    }

    public function getStudentById($id) {
        /**
         * Get a single student
         */
        return $this->getByMap('Students',$id);
    }

    public function getStudentByPersonId($personId) {
        /**
         * Get a single student
         */
        $url = self::API_URL . '/api/students?person=' . $personId;
        return $this->getByMap('Students',null,$url);
    }

    public function getStudents()
    {
        return $this->getByMap('Students');
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
        return $this->getByMap('StudentAcademicPrograms', null, $url);
    }

    public function getAcademicPeriodProfiles($personId, $academicPeriodId) {
        $url = self::API_URL . "/api/student-academic-period-profiles?person=" . $personId . "&academicPeriod=" . $academicPeriodId;
        return $this->getByMap('AcademicPeriodProfiles', null, $url);
    }



    /* Reference data API calls */

    public function getStudentType($id) {
        return $this->getByMap('StudentTypes', $id);
    }

    public function getStudentTypes() {
        return $this->getByMap('StudentTypes');
    }

    public function getAcademicProgram($id) {
        return $this->getByMap('AcademicPrograms', $id);
    }

    public function getAcademicPrograms() {
        return $this->getByMap('AcademicPrograms');
    }

    public function getInstitution($id) {
        return $this->getByMap('Institutions',$id);
    }

    public function getInstitutions() {
        return $this->getByMap('Institutions');
    }

    public function getAcademicLevel($id) {
        return $this->getByMap('AcademicLevels',$id);
    }

    public function getAcademicLevels() {
        return $this->getByMap('AcademicLevels');
    }

    public function getAcademicPeriod($id) {
        return $this->getByMap('AcademicPeriods',$id);
    }

    public function getAcademicPeriods() {
        return $this->getByMap('AcademicPeriods');
    }

    public function getSite($id) {
        return $this->getByMap('Sites',$id);
    }

    public function getSites() {
        return $this->getByMap('Sites');
    }

    public function getEnrollmentStatus($id) {
        return $this->getByMap('EnrolmentStatuses',$id); 
    }

    public function getEnrollmentStatuses() {
        return $this->getByMap('EnrolmentStatuses'); 
    }

    public function getAcademicCredential($id) {
        return $this->getByMap('AcademicCredentials',$id); 
    }

    public function getAcademicCredentials() {
        return $this->getByMap('AcademicCredentials'); 
    }

    public function getStudentStatus($id) {
        return $this->getByMap('StudentStatuses',$id);  
    }

    public function getStudentStatuses() {
        return $this->getByMap('StudentStatuses');  
    }

    public function getAcademicDiscipline($id) {
        return $this->getByMap('AcademicDisciplines',$id);
    }

    public function getAcademicDisciplines() {
        return $this->getByMap('AcademicDisciplines');
    }
}