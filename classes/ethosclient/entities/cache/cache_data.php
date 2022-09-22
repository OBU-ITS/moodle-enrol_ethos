<?php
namespace enrol_ethos\ethosclient\entities\cache ;

class cache_data
{
    public object $data;
    public float $expires;

    public function __construct(object $data, int $duration)
    {
        $this->data = $data;
        $this->expires = microtime(true) + $duration;
    }
}
