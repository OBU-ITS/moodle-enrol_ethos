<?php
namespace enrol_ethos\entities\deprecation;

class deprecated_resource
{
    public string $deprecatedOn = '';
    public string $description = '';
    public string $sunsetOn = '';
    public string $newVersionAvailable = '';
    public string $currentVersion = '';

    public function __construct()
    {
    }
}