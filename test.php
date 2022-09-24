<?php

use enrol_ethos\processors\base\obu_processor;

require_once('../../config.php');
require_once($CFG->libdir.'/weblib.php');

$processors = array();

$directory = realpath(__DIR__ . "/classes/processors");

echo "Dir: $directory <br />";

if(!is_dir($directory . "/")) {
    return;
}

foreach (array_filter(glob($directory . "/*.php"), 'is_file') as $file)
{
    echo "File: $file <br />";
    include $file;
}


$trace = new \null_progress_trace();
foreach(get_declared_classes() as $class) {
    $interfaces = class_implements($class);

    if (!isset($interfaces['enrol_ethos\processors\base\obu_processor'])) {
        continue;
    }

    $instance = new $class($trace);
    if ($instance instanceof obu_processor) {
        try {
            $constant_reflex = new \ReflectionClassConstant($class, 'RESOURCE_NAME');
            $resourceName = $constant_reflex->getValue();
        } catch (\ReflectionException $e) {
            $resourceName = '';
        }
        $processors[$resourceName] = $instance;
    }
}

array_map(function($item) {
    echo $item . "<br />";
}, array_keys($processors));