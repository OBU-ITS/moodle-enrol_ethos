<?php

namespace enrol_ethos\services;

class core_class_finder_service {
    /**
     * @param string $path sub-folder path
     */
    public static function includeFilesInFolder(string $path = "") {
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