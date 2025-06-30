<?php
/**
 * phpFormStep Library - Main Entry Point
 * 
 * This is the main entry point for the phpFormStep library.
 * Include this file to use the library in your project.
 * 
 * @package phpFormStep
 * @version 1.0.0
 * @author ChatFree Team
 * @license MIT
 */

// Prevent direct access
if (!defined('ABSPATH') && !defined('ADMIN_ACCESS') && php_sapi_name() !== 'cli') {
    // Allow access if we're in a known environment or CLI
    $allowed = false;
    
    // Check for common framework patterns
    if (defined('LARAVEL_START') || defined('WP_DEBUG') || defined('DRUPAL_ROOT')) {
        $allowed = true;
    }
    
    // Check for our specific application
    if (file_exists(__DIR__ . '/../../config/bootstrap.php')) {
        $allowed = true;
    }
    
    if (!$allowed) {
        http_response_code(403);
        die('Direct access to phpFormStep library is not allowed.');
    }
}

// Library information
const PHPFORMSTEP_VERSION = '1.0.0';
const PHPFORMSTEP_NAME = 'phpFormStep';
const PHPFORMSTEP_DESCRIPTION = 'Professional multi-step form library for PHP';

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    throw new Exception('phpFormStep requires PHP 7.4 or higher. Current version: ' . PHP_VERSION);
}

// Load autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    // Use Composer autoloader if available
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    // Use our custom autoloader
    require_once __DIR__ . '/autoload.php';
}

// Library loaded successfully
if (!defined('PHPFORMSTEP_LOADED')) {
    define('PHPFORMSTEP_LOADED', true);
}
