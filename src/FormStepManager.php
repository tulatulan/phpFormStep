<?php
/**
 * phpFormStep - Professional Multi-Step Form Library
 * Main Manager Class
 */

namespace phpFormStep;

// Library protection
if (!defined('PHPFORMSTEP_LOADED') && php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Direct access to phpFormStep library files is forbidden. Use the bootstrap.php entry point.');
}

class FormStepManager {
    
    private FormStepConfig $config;
    private FormStepState $state;
    private FormStepValidator $validator;
    private array $errors = [];
    private bool $isProcessed = false;
    
    /**
     * Constructor
     */
    public function __construct(FormStepConfig $config, string $formId = 'default') {
        $this->config = $config;
        $this->state = new FormStepState($config->sessionPrefix, $formId);
        $this->validator = new FormStepValidator();
        
        // Set validation rules from config
        foreach ($config->validationRules as $step => $rules) {
            $this->validator->setRules($step, $rules);
        }
        
        // Handle edit mode initialization
        if ($config->mode === 'edit' && $config->primaryKeyValue !== null) {
            $this->initializeEditMode();
        }
        
        // Process form if POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm();
        }
    }
    
    /**
     * Initialize edit mode
     */
    private function initializeEditMode(): void {
        // Store primary key in session
        $this->state->setPrimaryKeyValue($this->config->primaryKeyValue);
        
        // Mark init step as completed since we're editing
        $this->state->markStepCompleted($this->config->initStep);
        
        // Load existing data if available
        $this->loadExistingData();
    }
    
    /**
     * Load existing data from database (override in subclass)
     */
    protected function loadExistingData(): void {
        // Override this method to load data from database
        // Example:
        // $data = YourModel::find($this->config->primaryKeyValue);
        // $this->state->setStepData(1, $data->toArray());
    }
    
    /**
     * Process form submission
     */
    private function processForm(): void {
        $this->isProcessed = true;
        $action = $_POST['action'] ?? 'next';
        $currentStep = $this->getCurrentStep();
        
        // Clean POST data
        $postData = $_POST;
        unset($postData['action'], $postData['csrf_token']);
        
        // Handle different actions
        switch ($action) {
            case 'next':
                $this->processNextStep($postData);
                break;
                
            case 'prev':
                $this->processPreviousStep();
                break;
                
            case 'save':
                $this->processSaveStep($postData);
                break;
                
            case 'goto':
                $targetStep = (int)($_POST['target_step'] ?? $currentStep);
                $this->processGotoStep($targetStep, $postData);
                break;
                
            case 'complete':
                $this->processCompleteForm($postData);
                break;
        }
    }
    
    /**
     * Process next step
     */
    private function processNextStep(array $data): void {
        $currentStep = $this->getCurrentStep();
        
        // Validate current step
        if (!$this->validator->validate($currentStep, $data)) {
            $this->errors = $this->validator->getErrors();
            return;
        }
        
        // Save step data
        $this->state->setStepData($currentStep, $data);
        
        // Handle step processing
        $handler = $this->config->getStepHandler($currentStep);
        if ($handler) {
            $result = call_user_func($handler, $data, $this->state);
            if (!($result['success'] ?? true)) {
                $this->errors = $result['errors'] ?? ['Step processing failed'];
                return;
            }
        }
        
        // Mark step as completed
        $this->state->markStepCompleted($currentStep);
        
        // Handle init step (create primary key)
        if ($currentStep === $this->config->initStep && $this->config->mode === 'create') {
            if (!$this->state->isInitialized()) {
                $this->handleInitStep($data);
            }
        }
        
        // Move to next step
        if ($currentStep < $this->config->totalSteps) {
            $this->state->setCurrentStep($currentStep + 1);
        }
    }
    
    /**
     * Process previous step
     */
    private function processPreviousStep(): void {
        if (!$this->config->allowNavigation) {
            $this->errors[] = 'Navigation between steps is not allowed';
            return;
        }
        
        $currentStep = $this->getCurrentStep();
        if ($currentStep > 1) {
            $this->state->setCurrentStep($currentStep - 1);
        }
    }
    
    /**
     * Process save step (without navigation)
     */
    private function processSaveStep(array $data): void {
        $currentStep = $this->getCurrentStep();
        
        // Validate current step
        if (!$this->validator->validate($currentStep, $data)) {
            $this->errors = $this->validator->getErrors();
            return;
        }
        
        // Save step data
        $this->state->setStepData($currentStep, $data);
        
        // Handle step processing
        $handler = $this->config->getStepHandler($currentStep);
        if ($handler) {
            $result = call_user_func($handler, $data, $this->state);
            if (!($result['success'] ?? true)) {
                $this->errors = $result['errors'] ?? ['Step processing failed'];
                return;
            }
        }
        
        // Mark step as completed
        $this->state->markStepCompleted($currentStep);
        
        // Handle init step
        if ($currentStep === $this->config->initStep && $this->config->mode === 'create') {
            if (!$this->state->isInitialized()) {
                $this->handleInitStep($data);
            }
        }
    }
    
    /**
     * Process goto step
     */
    private function processGotoStep(int $targetStep, array $data): void {
        if (!$this->config->allowNavigation) {
            $this->errors[] = 'Navigation between steps is not allowed';
            return;
        }
        
        $currentStep = $this->getCurrentStep();
        
        // Check if target step is valid
        if ($targetStep < 1 || $targetStep > $this->config->totalSteps) {
            $this->errors[] = 'Invalid target step';
            return;
        }
        
        // Check if required save steps are completed
        for ($i = 1; $i < $targetStep; $i++) {
            if ($this->config->isRequiredSaveStep($i) && !$this->state->isStepCompleted($i)) {
                $this->errors[] = "Step {$i} must be completed before proceeding";
                return;
            }
        }
        
        // Save current step data if moving forward
        if ($targetStep > $currentStep) {
            $this->processSaveStep($data);
            if (!empty($this->errors)) {
                return;
            }
        }
        
        $this->state->setCurrentStep($targetStep);
    }
    
    /**
     * Process complete form
     */
    private function processCompleteForm(array $data): void {
        $currentStep = $this->getCurrentStep();
        
        // Must be on last step
        if ($currentStep !== $this->config->totalSteps) {
            $this->errors[] = 'Form can only be completed from the last step';
            return;
        }
        
        // Validate final step
        if (!$this->validator->validate($currentStep, $data)) {
            $this->errors = $this->validator->getErrors();
            return;
        }
        
        // Save final step data
        $this->state->setStepData($currentStep, $data);
        
        // Handle final step processing
        $handler = $this->config->getStepHandler($currentStep);
        if ($handler) {
            $result = call_user_func($handler, $data, $this->state);
            if (!($result['success'] ?? true)) {
                $this->errors = $result['errors'] ?? ['Final step processing failed'];
                return;
            }
        }
        
        // Mark as completed
        $this->state->markStepCompleted($currentStep);
    }
    
    /**
     * Handle init step (create primary key)
     */
    private function handleInitStep(array $data): void {
        // Override this method to create record in database
        // Example:
        // $id = YourModel::create($data);
        // $this->state->setPrimaryKeyValue($id);
        // $this->state->markAsInitialized();
        
        // For demo purposes, generate a fake ID
        $fakeId = rand(1000, 9999);
        $this->state->setPrimaryKeyValue($fakeId);
        $this->state->markAsInitialized();
    }
    
    /**
     * Get current step
     */
    public function getCurrentStep(): string {
        $currentStep = $this->state->getCurrentStep();
        
        // Initialize to first step if empty
        if (empty($currentStep)) {
            $currentStep = (string)$this->config->steps[0];
            $this->state->setCurrentStep($currentStep);
        }
        
        return $currentStep;
    }
    
    /**
     * Get step data
     */
    public function getStepData(string $step = null): array {
        $step = $step ?? $this->getCurrentStep();
        return $this->state->getStepData($step);
    }
    
    /**
     * Get all step data
     */
    public function getAllStepData(): array {
        return $this->state->getAllStepData();
    }
    
    /**
     * Get primary key value
     */
    public function getPrimaryKeyValue(): mixed {
        return $this->state->getPrimaryKeyValue();
    }
    
    /**
     * Check if step is completed
     */
    public function isStepCompleted(string $step): bool {
        return $this->state->isStepCompleted($step);
    }
    
    /**
     * Check if form is complete
     */
    public function isComplete(): bool {
        $currentStep = $this->getCurrentStep();
        $lastStep = end($this->config->steps);
        return $currentStep === (string)$lastStep && $this->isStepCompleted($currentStep);
    }
    
    /**
     * Get errors
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Check if processed
     */
    public function isProcessed(): bool {
        return $this->isProcessed;
    }
    
    /**
     * Render step content
     */
    public function renderStep(string $step = null): string {
        $step = $step ?? $this->getCurrentStep();
        
        $filePath = $this->config->getStepFile($step);
        if (!$filePath || !file_exists($filePath)) {
            return "<div class='alert alert-warning'>Step {$step} view file not found: {$filePath}</div>";
        }
        
        // Start output buffering
        ob_start();
        
        // Make variables available to view
        $formManager = $this;
        $stepData = $this->getStepData($step);
        $errors = $this->getErrors();
        $config = $this->config;
        
        // Include the step file
        include $filePath;
        
        // Return the captured output
        return ob_get_clean();
    }
    
    /**
     * Reset form
     */
    public function reset(): void {
        $this->state->reset();
        $this->errors = [];
        $this->isProcessed = false;
    }
}
