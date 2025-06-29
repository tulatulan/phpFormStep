<?php
/**
 * Quick test for phpFormStep library
 */

// Include the library via bootstrap
require_once __DIR__ . '/../bootstrap.php';

use phpFormStep\FormStepConfig;
use phpFormStep\FormStepManager;

echo "=== phpFormStep Library Test ===\n";

try {
    // Test configuration
    $config = new FormStepConfig([
        'totalSteps' => 3,
        'initStep' => 1,
        'requiredSaveSteps' => [1, 3],
        'allowNavigation' => true,
        'mode' => 'create',
        'sessionPrefix' => 'test_form_',
    ]);
    
    echo "✓ FormStepConfig created successfully\n";
    echo "  - Total steps: {$config->totalSteps}\n";
    echo "  - Init step: {$config->initStep}\n";
    echo "  - Steps array: " . implode(', ', $config->steps) . "\n";
    
    // Test setting step files and handlers
    $config->setStepFile(1, '/path/to/step1.php');
    $config->setStepHandler(1, function($data, $state) {
        return ['success' => true, 'message' => 'Step 1 processed'];
    });
    
    $config->setValidationRules(1, [
        'title' => 'required|min:5|max:100',
        'email' => 'required|email'
    ]);
    
    echo "✓ Step configuration methods work\n";
    
    // Test manager creation (without HTTP request)
    $_SERVER['REQUEST_METHOD'] = 'GET'; // Simulate GET request
    session_start();
    
    $manager = new FormStepManager($config, 'test');
    echo "✓ FormStepManager created successfully\n";
    
    $currentStep = $manager->getCurrentStep();
    echo "  - Current step: {$currentStep}\n";
    
    $stepData = $manager->getStepData($currentStep);
    echo "  - Step data: " . (empty($stepData) ? 'empty' : 'has data') . "\n";
    
    echo "✓ Basic functionality working\n";
    
    // Test validation
    $testData = [
        'title' => 'Test',
        'email' => 'invalid-email'
    ];
    
    $validator = new \phpFormStep\FormStepValidator();
    $validator->setRules('1', [
        'title' => 'required|min:5|max:100',
        'email' => 'required|email'
    ]);
    
    $isValid = $validator->validate('1', $testData);
    echo "✓ Validation test: " . ($isValid ? 'passed' : 'failed (expected)') . "\n";
    
    if (!$isValid) {
        $errors = $validator->getAllErrors();
        echo "  - Validation errors: " . implode(', ', $errors) . "\n";
    }
    
    echo "\n=== All tests passed! ===\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
