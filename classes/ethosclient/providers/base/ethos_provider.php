<?php
namespace enrol_ethos\ethosclient\providers\base;

use enrol_ethos\ethosclient\client\ethos_client;
use enrol_ethos\ethosclient\entities\cache\cache_settings;
use enrol_ethos\ethosclient\services\cache\cache_service;

abstract class ethos_provider
{
    private string $path;
    private string $acceptHeader;
    private bool $cacheable;
    private ?cache_settings $cacheSettings;

    protected ethos_client $ethosClient;
    protected cache_service $cacheService;

    protected function __construct()
    {
        $this->ethosClient = ethos_client::getInstance();
        $this->cacheService = cache_service::getInstance();
    }

    /**
     * @param string $path ethos api path
     * @param string $version ethos api path version
     * @param int|null $cacheDuration cache duration in seconds
     */
    protected function prepareProvider(string $path, string $version, ?int $cacheDuration = null) {
        $this->path = $path;
        $this->acceptHeader = $this->buildAcceptHeader($version);

        if(isset($cacheDuration)) {
            $this->cacheable = true;
            $this->cacheSettings = new cache_settings($path, $cacheDuration);
        }
        else {
            $this->cacheable = false;
        }
    }

    private function buildAcceptHeader($version) : string {
        return "application/vnd.hedtech.integration.$version+json";
    }

    protected function buildUrlWithCriteria($criteria) : string {
        return ethos_client::API_URL . "/api/" . $this->path . "?criteria=" . $criteria;
    }

    protected function getFromEthosById(string $id, bool $paged=null, int $maxResults=0) : ?object {
        if ($this->cacheable && $valueFromCache = $this->cacheService->getFromCache($id, $this->cacheSettings->collection)) {
            return $valueFromCache;
        }
        else if(!$this->cacheable) {
            echo "Not cachable : $this->path <br />";
        }

        $result = $this->getFromEthosClient($id, null, $paged, $maxResults);

        if ($this->cacheable) {
            $this->cacheService->addToCache($id, $result, $this->cacheSettings);
        }

        return $result;
    }

    protected function getFromEthos(string $urlOverride=null, bool $paged=null, int $maxResults=0, int $offset=0) : ?array {
        return $this->getFromEthosClient(null, $urlOverride, $paged, $maxResults, $offset);

//        if ($this->cacheable) {
//            foreach ($result as $res) {
//                $this->cacheService->addToCache($res->id, $res, $this->cacheSettings);
//            }
//        }
    }

    private function getFromEthosClient(string $id=null, string $urlOverride=null, bool $paged=null, int $maxResults=0, int $offset=0)
    {
        $url = $urlOverride ?: ethos_client::API_URL . "/api/" . $this->path;

        if (!$urlOverride) {
            if ($id) {
                $url = $url . "/$id";
                $paged = $paged === null ? false : $paged;
            } else {
                $paged = $paged === null ? true : $paged;
            }
        }

        return !$paged
            ? $this->ethosClient->getJson($url, $this->acceptHeader)
            : $this->ethosClient->getJson($url, $this->acceptHeader,$maxResults,500, $offset);
    }
}