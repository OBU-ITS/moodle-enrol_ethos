<?php

namespace enrol_ethos\ethosclient\general;

class class_finder {
    /**
     * @param string $path subfolder path
     */
    public static function includeAllFilesEthosClient(string $path = "") {
        $directory = $path == ""
            ? realpath(__DIR__ . "/../.")
            : realpath(__DIR__ . "/../" . $path);

        if(!is_dir($directory)) {
            return;
        }

        foreach (array_filter(glob($directory . "/*.php"), 'is_file') as $file)
        {
            include $file;
        }
    }
}