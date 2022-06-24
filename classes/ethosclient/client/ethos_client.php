<?php

namespace enrol_ethos\ethosclient\client;

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ethos_client
{
    const API_URL = "https://integrate.elluciancloud.ie";
    const verify = false;

    private ?Client $client = null;
    private string $authKey;
    private string $accessToken;

    /**
     * @throws Exception when API key cannot be found
     */
    private function __construct()
    {
        $this->authKey = $this->getApiKey();
        $this->client = new Client();
    }

    private static ?ethos_client $instance = null;
    public static function getInstance() : ethos_client
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $url API endpoint
     * @param string $accept Request Accept header
     * @param int $maxResults maximum results returned by API
     * @param int $resultsPerPage results per page
     * @return array|mixed Repsonse contents
     * @throws Exception
     */
    public function getJson(string $url, string $accept, int $maxResults = 0, int $resultsPerPage = 0) {
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

    /**
     * @return string Ethos API Key
     * @throws Exception
     */
    private function getApiKey() : string {
        $apiKey = get_config('enrol_ethos', 'ethosapikey');

        if (!$apiKey) {
            throw new Exception('Ethos API key not set');
        }

        return $apiKey;
    }

    /**
     * @param string $url API endpoint
     * @param string $accept Request accept Header
     * @return string response contents
     * @throws Exception|RequestException if max retries are reached
     */
    private function get(string $url, string $accept) : string {

        $maxTries = 3;
        $tries = 0;

        while ($tries < $maxTries) {

            try {
                $options = [
                    'verify' => self::verify,
                    'headers' => [
                        'Content-Type'      => 'application/json',
                        'Accept-Charset'    => 'UTF-8',
                        'Accept'            => $accept,
                        'Authorization'     => 'Bearer ' . $this->getAccessToken()
                    ],
                ];

                $response = $this->client->getAsync($url, $options)->wait();

                return $response->getBody()->getContents();

            } catch (RequestException $e) {
                // Handle non 200 responses - includes regenerating access token if necessary

                $statusCode = $e->getResponse()->getStatusCode();

                switch ($statusCode) {
                    case '400':
                    case '401':
                        $this->prepareAccessToken();
                        break;
                    default:
                        return $e->getResponse()->getBody()->getContents();
                }

                if (++$tries == $maxTries) {
                    // Max consecutive errors - not solved by retrying.
                    // TODO: Exponential back off.
                    throw $e;
                }
            }
        }
    }

    /**
     * @throws Exception|RequestException
     */
    private function prepareAccessToken()
    {
        try {

            $url = self::API_URL . '/auth';
            $options = [
                'verify' => self::verify,
                'headers' => [
                    'Authorization'      => "Bearer " . $this->authKey,
                    'Cache-Control'      => 'no-cache'
                ],
            ];

            $response = $this->client->postAsync($url, $options)->wait();
            $this->accessToken = $response->getBody()->getContents();
        } catch (RequestException $e) {
            //TODO: Retry logic
            throw $e;
        }
    }

    /**
     * @return string Access token
     * @throws Exception|RequestException
     */
    private function getAccessToken() : string {
        if (!$this->accessToken) {
            $this->prepareAccessToken();
        }

        return $this->accessToken;
    }
}