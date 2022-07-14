<?php
namespace enrol_ethos\services;

use enrol_ethos\ethosclient\entities\ethos_section_info;
use enrol_ethos\ethosclient\providers\ethos_section_provider;

class obu_module_run_service
{
    private ethos_section_provider $sectionProvider;

    public function __construct()
    {
        $this->sectionProvider = ethos_section_provider::getInstance();
    }

    /**
     * Get module from Ethos by the Ethos section guid
     *
     * @param string $id ethos guid
     * @return ethos_section_info
     */
    public function getModuleById(string $id) : ethos_section_info {
        $module = $this->sectionProvider->get($id);
    }

    /**
     * Get all modules from Ethos
     *
     * @return ethos_section_info[]
     */
    public function getModules() : array {
    }
}