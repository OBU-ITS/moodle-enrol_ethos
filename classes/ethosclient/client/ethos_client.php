<?php

namespace enrol_ethos\ethosclient\client;

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use enrol_ethos\ethosclient\entities\request\ethos_response;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ethos_client
{
    const API_URL = "https://integrate.elluciancloud.ie";
    const verify = false;

    private ?Client $client = null;
    private string $authKey;
    private ?string $accessToken = null;

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
     * @return array|mixed Response contents
     */
    public function getJson(string $url, string $accept, int $maxResults = 0, int $resultsPerPage = 0, int $initialOffset = 0) {
        if ((!$resultsPerPage) || ($maxResults && ($maxResults <= $resultsPerPage))) {
            $limit = ($maxResults && ($maxResults <= $resultsPerPage)) ? $maxResults : $resultsPerPage;
            $url1 = $url;
            if($limit > 0 || $initialOffset > 0) {
                if ($limit > 0) {
                    $qJoin = strpos($url, '?') ? '&' : '?';
                    $url1 = "{$url}{$qJoin}limit=$limit";
                }
                if ($initialOffset > 0) {
                    $qJoin = strpos($url1, '?') ? '&' : '?';
                    $url1 = "{$url1}{$qJoin}offset=$initialOffset";
                }
            }

            try {
                return $this->get($url1, $accept);
            } catch (Exception $e) {
                return null;
            }
        }

        $maxResults = $maxResults ?: 100000;

        $results = array();

        for ($offset = $initialOffset; $offset < ($initialOffset + $maxResults); $offset+=$resultsPerPage) {
            $qJoin = strpos($url,'?') ? '&' : '?';
            $url1 = "{$url}{$qJoin}limit=$resultsPerPage&offset=$offset";

            try {
                $jsonResult = $this->get($url1, $accept);
            } catch (Exception $e) {
                break;
            }

            $results = array_merge($jsonResult->messages,$results);

            if (count($jsonResult->messages) < $resultsPerPage) {
                break;
            }
        }

        return new ethos_response($results);
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
     * @throws Exception|RequestException if max retries are reached
     */
    private function get(string $url, string $accept) : ?ethos_response {

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

                $decodedMessages = json_decode($response->getBody()->getContents());
                foreach ($response->getHeaders() as $name => $values) {
                    if($name == "x-remaining") {
                        return new ethos_response($decodedMessages, implode(', ', $values));
                    }
                }

                return new ethos_response($decodedMessages);

            } catch (RequestException $e) {
                // Handle non 200 responses - includes regenerating access token if necessary

                $statusCode = $e->getResponse()->getStatusCode();

                switch ($statusCode) {
                    case '400':
                    case '401':
                        $this->prepareAccessToken();
                        break;
                    default:
                        throw $e;
                }

                if (++$tries == $maxTries) {
                    // Max consecutive errors - not solved by retrying.
                    // TODO: Exponential back off.
                    throw $e;
                }
            }
        }

        return null;
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