<?php
namespace enrol_ethos\ethosclient\services\cache;

use cache;
use cache_store;
use enrol_ethos\ethosclient\entities\cache\cache_data;
use enrol_ethos\ethosclient\entities\cache\cache_settings;

class cache_service {
    const DEFAULT_COLLECTION = "default";

    private static cache $cache;

    private float $clearDownTime;

    private function __construct()
    {
        self::$cache = cache::make_from_params(cache_store::MODE_APPLICATION,'enrol_ethos', 'ethos_client');
    }

    /**
     * Get ethos client instance
     *
     * @return cache_service
     */
    private static ?cache_service $instance = null;
    public static function getInstance() : cache_service
    {
        if (self::$instance == null)
        {
            self::$instance = new cache_service();
        }

        return self::$instance;
    }

    /**
     * Add to cache by full parameters
     *
     * @param string $key
     * @param object $data
     * @param string $collection
     * @param string $duration in milliseconds
     */
    public function addToCacheExpanded(string $key, object $data, string $collection, string $duration) {
        $cacheKey = $this->getCacheKey($key, $collection);

        self::$cache->set($cacheKey, new cache_data($data, $duration));
    }

    /**
     * Add to cache by settings
     *
     * @param string $key
     * @param object $data
     * @param cache_settings $settings
     */
    public function addToCache(string $key, object $data, cache_settings $settings) {
        $this->addToCacheExpanded($key, $data, $settings->collection, $settings->duration);
    }

    /**
     * @param string $key
     * @param string $collection
     * @return object|null cached data or null if not in cache
     */
    public function getFromCache(string $key, string $collection) : ?object {

        $cacheKey = $this->getCacheKey($key, $collection);

        if($data = self::$cache->get($cacheKey)) {
            if ($data->expires >= microtime(true)) {
                return $data->data;
            }
            else{
                self::$cache->delete($cacheKey);
            }
        }

        return null;
    }

    /**
     * Remove expired data from the cache
     *
     * @param cache_data $data current cache data
     * @return bool whether data should be removed from the cache
     */
    private function removeExpiredData(cache_data $data) : bool {
        return $data->expires > $this->clearDownTime;
    }

    /**
     * @param string $key
     * @param string $collection
     * @return string generated cache key
     */
    private function getCacheKey(string $key, string $collection) : string {
        return $collection . "_" . $key;
    }
}