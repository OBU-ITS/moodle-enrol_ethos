<?php

namespace enrol_ethos\helpers;

class core_class_finder_helper {
    /**
     * @param string $path sub-folder path
     */
    public static function includeFilesInFolder(string $path = "") {
        $directory = realpath(__DIR__ . "/../" . $path);
        if(!is_dir($directory)) {
            return;
        }

        foreach (array_filter(glob($directory . "/*.php"), 'is_file') as $file)
        {
            include $file;
        }
    }
}