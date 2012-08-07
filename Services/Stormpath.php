<?php

function Services_Stormpath_autoload($className) {
    if (substr($className, 0, 18) != 'Services_Stormpath') {
        return false;
    }
    $file = str_replace('_', '/', $className);
    $file = str_replace('Services/', '', $file);
    return include dirname(__FILE__) . "/$file.php";
}

spl_autoload_register('Services_Stormpath_autoload');