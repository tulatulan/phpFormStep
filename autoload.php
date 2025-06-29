<?php
/**
 * phpFormStep Library - Professional Autoloader
 * 
 * This file provides a simple autoloader for the phpFormStep library
 * when Composer is not available.
 * 
 * @package phpFormStep
 * @version 1.0.0
 * @author ChatFree Team
 * @license MIT
 */

spl_autoload_register(function ($class) {
    // Check if this is a phpFormStep class
    $prefix = 'phpFormStep\\';
    $base_dir = __DIR__ . '/src/';

    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Define library constants
if (!defined('PHPFORMSTEP_VERSION')) {
    define('PHPFORMSTEP_VERSION', '1.0.0');
}

if (!defined('PHPFORMSTEP_ROOT')) {
    define('PHPFORMSTEP_ROOT', __DIR__);
}
