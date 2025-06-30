<?php
/**
 * phpFormStep - Professional Multi-Step Form Library
 * Configuration Class
 */

namespace phpFormStep;

// Library protection
if (!defined('PHPFORMSTEP_LOADED') && php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Direct access to phpFormStep library files is forbidden. Use the bootstrap.php entry point.');
}

class FormStepConfig {
    
    public $steps = [];
    public $initStep = '';
    public $requiredSaveSteps = [];
    public $allowNavigation = true;
    public $stepFiles = [];
    public $stepHandlers = [];
    public $mode = 'create'; // 'create' or 'edit'
    public $validationRules = [];
    public $sessionPrefix = 'form_step_';
    public $primaryKeyValue = null;
    
    // Additional properties for backward compatibility
    public $totalSteps = 0;
    public $tableName = '';
    
    /**
     * Constructor
     */
    public function __construct($config = []) {
        // Set defaults
        $this->steps = $config['steps'] ?? [];
        $this->initStep = $config['initStep'] ?? ($this->steps[0] ?? '');
        $this->requiredSaveSteps = $config['requiredSaveSteps'] ?? [];
        $this->allowNavigation = $config['allowNavigation'] ?? true;
        $this->stepFiles = $config['stepFiles'] ?? [];
        $this->stepHandlers = $config['stepHandlers'] ?? [];
        $this->mode = $config['mode'] ?? 'create';
        $this->validationRules = $config['validationRules'] ?? [];
        $this->sessionPrefix = $config['sessionPrefix'] ?? 'form_step_';
        $this->primaryKeyValue = $config['primaryKeyValue'] ?? null;
        $this->totalSteps = $config['totalSteps'] ?? 0;
        $this->tableName = $config['tableName'] ?? '';
        
        // Generate steps array from totalSteps if not provided
        if (empty($this->steps) && $this->totalSteps > 0) {
            $this->steps = range(1, $this->totalSteps);
        }
        
        // Set initStep from numeric value if needed
        if (is_numeric($this->initStep)) {
            $this->initStep = (string)$this->initStep;
        }
        
        // Validate configuration
        $this->validate();
    }
    
    /**
     * Validate configuration
     */
    private function validate(): void {
        if (empty($this->steps)) {
            throw new \InvalidArgumentException('Steps array cannot be empty');
        }
        
        if (!empty($this->initStep) && !in_array($this->initStep, $this->steps)) {
            throw new \InvalidArgumentException('Init step must be one of the defined steps');
        }
        
        foreach ($this->requiredSaveSteps as $step) {
            if (!in_array($step, $this->steps)) {
                throw new \InvalidArgumentException("Required save step '{$step}' must be one of the defined steps");
            }
        }
        
        if (!in_array($this->mode, ['create', 'edit'])) {
            throw new \InvalidArgumentException('Mode must be either "create" or "edit"');
        }
    }
    
    /**
     * Get step index
     */
    public function getStepIndex(string $step): int {
        $index = array_search($step, $this->steps);
        return $index !== false ? $index : -1;
    }
    
    /**
     * Get next step
     */
    public function getNextStep(string $currentStep): ?string {
        $index = $this->getStepIndex($currentStep);
        if ($index === -1 || $index >= count($this->steps) - 1) {
            return null;
        }
        return $this->steps[$index + 1];
    }
    
    /**
     * Get previous step
     */
    public function getPreviousStep(string $currentStep): ?string {
        $index = $this->getStepIndex($currentStep);
        if ($index <= 0) {
            return null;
        }
        return $this->steps[$index - 1];
    }
    
    /**
     * Check if step is required for save
     */
    public function isRequiredSaveStep(string $step): bool {
        return in_array($step, $this->requiredSaveSteps);
    }
    
    /**
     * Get step file path
     */
    public function getStepFile(string $step): ?string {
        return $this->stepFiles[$step] ?? null;
    }
    
    /**
     * Get step handler path
     */
    public function getStepHandler(string $step): ?string {
        return $this->stepHandlers[$step] ?? null;
    }
    
    /**
     * Check if this is the first step
     */
    public function isFirstStep(string $step): bool {
        return $this->getStepIndex($step) === 0;
    }
    
    /**
     * Check if this is the last step
     */
    public function isLastStep(string $step): bool {
        return $this->getStepIndex($step) === count($this->steps) - 1;
    }
    
    /**
     * Set step file path
     */
    public function setStepFile($step, string $filePath): void {
        $this->stepFiles[(string)$step] = $filePath;
    }
    
    /**
     * Set step handler
     */
    public function setStepHandler($step, callable $handler): void {
        $this->stepHandlers[(string)$step] = $handler;
    }
    
    /**
     * Set validation rules for a step
     */
    public function setValidationRules($step, array $rules): void {
        $this->validationRules[(string)$step] = $rules;
    }
}