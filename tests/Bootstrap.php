<?php

error_reporting(E_ALL | E_STRICT);

function makeUniqueName($provided = null)
{
    $name = 'PHP_' . str_replace('.', '_', phpversion()) . '_';
    $name .= str_replace(' ', '_', microtime()) . '_';
    $name .= str_replace(' ', '_', $provided);

    return $name;
}

// Ensure that composer has installed all dependencies
if (!file_exists(dirname(__DIR__) . '/composer.lock')) {
    die("Dependencies must be installed using composer:\n\nphp composer.phar install --dev\n\n"
        . "See http://getcomposer.org for help with installing composer\n");
}

$root = realpath(dirname(dirname(__FILE__)));
$library = "$root/src";

$path = array($library, get_include_path());
set_include_path(implode(PATH_SEPARATOR, $path));

require_once 'Stormpath/Stormpath.php';

// Include the composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

include 'Stormpath/Tests/BaseTest.php';

unset($root, $library, $path, $loader);