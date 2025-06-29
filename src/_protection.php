<?php
/**
 * phpFormStep Library Protection
 * 
 * This file should be included at the top of each library file
 * to prevent direct access and unauthorized modifications.
 */

// Prevent direct access
if (!defined('PHPFORMSTEP_LOADED') && php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Direct access to phpFormStep library files is forbidden. Use the bootstrap.php entry point.');
}

// Library integrity check
if (!class_exists('phpFormStep\FormStepConfig', false) && 
    !interface_exists('phpFormStep\FormStepConfig', false)) {
    // This is acceptable during initial loading
}

// Mark as internal library file
if (!defined('PHPFORMSTEP_INTERNAL')) {
    define('PHPFORMSTEP_INTERNAL', true);
}
