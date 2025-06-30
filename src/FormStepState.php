<?php
/**
 * phpFormStep - Professional Multi-Step Form Library
 * State Management Class
 */

namespace phpFormStep;

// Library protection
if (!defined('PHPFORMSTEP_LOADED') && php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Direct access to phpFormStep library files is forbidden. Use the bootstrap.php entry point.');
}

class FormStepState {
    
    private $sessionKey;
    private $state;
    
    /**
     * Constructor
     */
    public function __construct($sessionPrefix = 'form_step_', $formId = 'default') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->sessionKey = $sessionPrefix . $formId;
        $this->initializeState();
    }
    
    /**
     * Initialize state
     */
    private function initializeState(): void {
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [
                'currentStep' => '',
                'stepData' => [],
                'completedSteps' => [],
                'primaryKeyValue' => null,
                'created' => time()
            ];
        }
        $this->state = &$_SESSION[$this->sessionKey];
    }
    
    /**
     * Get current step
     */
    public function getCurrentStep(): string {
        return $this->state['currentStep'] ?? '';
    }
    
    /**
     * Set current step
     */
    public function setCurrentStep(string $step): void {
        $this->state['currentStep'] = $step;
    }
    
    /**
     * Get step data
     */
    public function getStepData(string $step): array {
        return $this->state['stepData'][$step] ?? [];
    }
    
    /**
     * Set step data
     */
    public function setStepData(string $step, array $data): void {
        $this->state['stepData'][$step] = $data;
    }
    
    /**
     * Get all step data
     */
    public function getAllStepData(): array {
        return $this->state['stepData'] ?? [];
    }
    
    /**
     * Merge step data
     */
    public function mergeStepData(string $step, array $data): void {
        if (!isset($this->state['stepData'][$step])) {
            $this->state['stepData'][$step] = [];
        }
        $this->state['stepData'][$step] = array_merge($this->state['stepData'][$step], $data);
    }
    
    /**
     * Check if step is completed
     */
    public function isStepCompleted(string $step): bool {
        return in_array($step, $this->state['completedSteps'] ?? []);
    }
    
    /**
     * Mark step as completed
     */
    public function markStepCompleted(string $step): void {
        if (!$this->isStepCompleted($step)) {
            $this->state['completedSteps'][] = $step;
        }
    }
    
    /**
     * Get completed steps
     */
    public function getCompletedSteps(): array {
        return $this->state['completedSteps'] ?? [];
    }
    
    /**
     * Get primary key value
     */
    public function getPrimaryKeyValue(): ?int {
        return $this->state['primaryKeyValue'];
    }
    
    /**
     * Set primary key value
     */
    public function setPrimaryKeyValue(?int $value): void {
        $this->state['primaryKeyValue'] = $value;
    }
    
    /**
     * Clear all state
     */
    public function clear(): void {
        unset($_SESSION[$this->sessionKey]);
        $this->initializeState();
    }
    
    /**
     * Clear step data
     */
    public function clearStepData(string $step): void {
        unset($this->state['stepData'][$step]);
    }
    
    /**
     * Get state creation time
     */
    public function getCreatedTime(): int {
        return $this->state['created'] ?? 0;
    }
    
    /**
     * Get combined data for database save
     */
    public function getCombinedData(): array {
        $combined = [];
        foreach ($this->state['stepData'] as $stepData) {
            $combined = array_merge($combined, $stepData);
        }
        return $combined;
    }
    
    /**
     * Remove completed step
     */
    public function removeCompletedStep(string $step): void {
        $this->state['completedSteps'] = array_filter(
            $this->state['completedSteps'],
            fn($s) => $s !== $step
        );
        $this->state['completedSteps'] = array_values($this->state['completedSteps']);
    }
}