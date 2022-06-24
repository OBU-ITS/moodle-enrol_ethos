<?php
namespace enrol_ethos\ethosclient\entities\cache ;

class cache_settings
{
    public string $collection;
    public string $duration;

    /**
     * cache_settings constructor.
     *
     * @param string $collection
     * @param string $duration in seconds
     */
    public function __construct(string $collection, string $duration)
    {
        $this->collection = $collection;
        $this->duration = $duration;
    }
}