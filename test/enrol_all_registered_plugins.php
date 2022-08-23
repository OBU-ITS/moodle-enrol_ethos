<?php

require_once('../../../config.php');
require_once($CFG->libdir . '/enrollib.php');

$plugins = enrol_get_plugins(false);

foreach($plugins as $plugin) {
    echo $plugin->get_name() . "<br/>";
}