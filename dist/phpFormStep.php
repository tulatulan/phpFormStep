<?php
/**
 * phpFormStep - Professional Multi-Step Form Library
 * 
 * A complete solution for creating multi-step forms with navigation,
 * validation, and data persistence.
 * 
 * @version 2.0.0
 * @author ChatFree Team
 * @license MIT
 */

class phpFormStep {
    
    private $config;
    private $currentStep;
    private $sessionPrefix;
    private $mode; // 'create' or 'edit'
    private $totalSteps;
    private $steps;
    private $errors = [];
    private $debug = false;
    
    /**
     * Constructor
     * 
     * @param array $config Configuration array
     */
    public function __construct($config = []) {
        $this->validateConfig($config);
        $this->config = $config;
        $this->mode = $config['mode'] ?? 'create';
        $this->totalSteps = $config['totalSteps'] ?? 1;
        $this->steps = $config['steps'] ?? [];
        $this->sessionPrefix = $config['sessionPrefix'] ?? 'phpFormStep_';
        $this->debug = $config['debug'] ?? false;
        
        // Initialize session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Determine current step
        $this->determineCurrentStep();
        
        // Process form if submitted
        if ($this->isFormSubmitted()) {
            $this->processForm();
        }
    }
    
    /**
     * Validate configuration
     */
    private function validateConfig($config) {
        if (!is_array($config)) {
            throw new InvalidArgumentException('Configuration must be an array');
        }
        
        if (!isset($config['steps']) || !is_array($config['steps'])) {
            throw new InvalidArgumentException('Steps configuration is required and must be an array');
        }
        
        if (!isset($config['totalSteps']) || !is_numeric($config['totalSteps'])) {
            throw new InvalidArgumentException('Total steps must be specified and numeric');
        }
        
        if (!isset($config['mode']) || !in_array($config['mode'], ['create', 'edit'])) {
            throw new InvalidArgumentException('Mode must be either "create" or "edit"');
        }
    }
    
    /**
     * Determine current step based on mode and session
     */
    private function determineCurrentStep() {
        if ($this->mode === 'create') {
            $this->currentStep = $_SESSION[$this->sessionPrefix . 'current_step'] ?? ($this->config['initStep'] ?? 1);
        } else {
            $this->currentStep = $_SESSION[$this->sessionPrefix . 'current_step'] ?? ($this->config['editStep'] ?? 1);
        }
        
        // Validate step bounds
        if ($this->currentStep < 1) {
            $this->currentStep = 1;
        } elseif ($this->currentStep > $this->totalSteps) {
            $this->currentStep = $this->totalSteps;
        }
    }
    
    /**
     * Check if form is submitted
     */
    private function isFormSubmitted() {
        return $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST);
    }
    
    /**
     * Process form submission
     */
    private function processForm() {
        $action = $_POST['formstep_action'] ?? '';
        
        switch ($action) {
            case 'next':
                $this->processNext();
                break;
            case 'prev':
                $this->processPrevious();
                break;
            case 'goto':
                $this->processGoto();
                break;
            case 'submit':
                $this->processSubmit();
                break;
        }
    }
    
    /**
     * Process next step
     */
    private function processNext() {
        $stepConfig = $this->steps[$this->currentStep] ?? [];
        $buttonNext = $stepConfig['buttonNext'] ?? [];
        
        if ($this->debug) {
            error_log("DEBUG: Processing next step - Current: " . $this->currentStep);
        }
        
        // Check if validation is required
        if ($buttonNext['required'] ?? false) {
            if (!$this->validateStep($this->currentStep)) {
                if ($this->debug) {
                    error_log("DEBUG: Validation failed for step " . $this->currentStep);
                }
                return;
            }
        }
        
        // Check if submit before continue
        if ($buttonNext['submitBeforeContinue'] ?? false) {
            if (!$this->submitStepData($this->currentStep)) {
                if ($this->debug) {
                    error_log("DEBUG: Submit failed for step " . $this->currentStep);
                }
                return;
            }
        }
        
        // Save step data to session
        $this->saveStepData($this->currentStep);
        
        // Move to next step
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $_SESSION[$this->sessionPrefix . 'current_step'] = $this->currentStep;
            
            if ($this->debug) {
                error_log("DEBUG: Moved to step " . $this->currentStep);
            }
        }
    }
    
    /**
     * Process previous step
     */
    private function processPrevious() {
        $stepConfig = $this->steps[$this->currentStep] ?? [];
        $buttonPrev = $stepConfig['buttonPrev'] ?? [];
        
        // Check if should clear input
        if ($buttonPrev['clearInput'] ?? false) {
            $this->clearStepData($this->currentStep);
        }
        
        // Move to previous step
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $_SESSION[$this->sessionPrefix . 'current_step'] = $this->currentStep;
        }
    }
    
    /**
     * Process goto step
     */
    private function processGoto() {
        $targetStep = (int)($_POST['target_step'] ?? $this->currentStep);
        
        if ($targetStep >= 1 && $targetStep <= $this->totalSteps) {
            $this->currentStep = $targetStep;
            $_SESSION[$this->sessionPrefix . 'current_step'] = $this->currentStep;
        }
    }
    
    /**
     * Process final form submission
     */
    private function processSubmit() {
        $stepConfig = $this->steps[$this->currentStep] ?? [];
        $handleSubmit = $stepConfig['handleSubmit'] ?? [];
        
        // Validate current step
        if (!$this->validateStep($this->currentStep)) {
            return;
        }
        
        // Save current step data
        $this->saveStepData($this->currentStep);
        
        // Submit to handler
        if (isset($handleSubmit['URLhandle'])) {
            $this->submitToHandler($handleSubmit);
        }
    }
    
    /**
     * Validate step data
     */
    private function validateStep($step) {
        $stepConfig = $this->steps[$step] ?? [];
        $buttonNext = $stepConfig['buttonNext'] ?? [];
        
        if (isset($buttonNext['validate']) && is_callable($buttonNext['validate'])) {
            $result = call_user_func($buttonNext['validate'], $_POST, $this->getStepData($step));
            
            if ($result !== true) {
                $this->errors = is_array($result) ? $result : ['Validation failed'];
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Submit step data to handler
     */
    private function submitStepData($step) {
        $stepConfig = $this->steps[$step] ?? [];
        $handleSubmit = $stepConfig['handleSubmit'] ?? [];
        
        if (isset($handleSubmit['URLhandle'])) {
            return $this->submitToHandler($handleSubmit);
        }
        
        return true;
    }
    
    /**
     * Submit to external handler
     */
    private function submitToHandler($handleSubmit) {
        $url = $handleSubmit['URLhandle'];
        $inputKey = $handleSubmit['inputKeyForForm'] ?? 'step' . $this->currentStep;
        
        // Prepare data
        $postData = $_POST;
        $postData[$inputKey] = $this->currentStep;
        
        // Submit via cURL or redirect
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            // External URL - use cURL
            $response = $this->curlPost($url, $postData);
            
            if ($response['success']) {
                if (isset($handleSubmit['successRedirectURL'])) {
                    header('Location: ' . $handleSubmit['successRedirectURL']);
                    exit;
                }
                return true;
            } else {
                if (isset($handleSubmit['falseRedirectURL'])) {
                    header('Location: ' . $handleSubmit['falseRedirectURL']);
                    exit;
                }
                $this->errors[] = 'Submission failed';
                return false;
            }
        } else {
            // Local file - include
            $_POST[$inputKey] = $this->currentStep;
            
            // Capture output to prevent interference
            ob_start();
            $result = include $url;
            ob_end_clean();
            
            // If no explicit return, assume success
            if ($result === 1 || $result === true) {
                return true;
            } elseif ($result === false) {
                $this->errors[] = 'Handler execution failed';
                return false;
            } else {
                // No explicit return from include, assume success
                return true;
            }
        }
    }
    
    /**
     * cURL POST request
     */
    private function curlPost($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => ($httpCode >= 200 && $httpCode < 300),
            'response' => $response,
            'httpCode' => $httpCode
        ];
    }
    
    /**
     * Save step data to session
     */
    private function saveStepData($step) {
        $stepConfig = $this->steps[$step] ?? [];
        $inputKey = $stepConfig['handleSubmit']['inputKeyForForm'] ?? 'step' . $step;
        
        $_SESSION[$this->sessionPrefix . 'step_' . $step] = $_POST;
        $_SESSION[$this->sessionPrefix . 'step_' . $step . '_key'] = $inputKey;
    }
    
    /**
     * Get step data from session
     */
    private function getStepData($step) {
        return $_SESSION[$this->sessionPrefix . 'step_' . $step] ?? [];
    }
    
    /**
     * Clear step data from session
     */
    private function clearStepData($step) {
        unset($_SESSION[$this->sessionPrefix . 'step_' . $step]);
        unset($_SESSION[$this->sessionPrefix . 'step_' . $step . '_key']);
    }
    
    /**
     * Render the form step
     */
    public function render() {
        $stepConfig = $this->steps[$this->currentStep] ?? [];
        
        // Start output buffering
        ob_start();
        
        // Render progress bar
        $this->renderProgressBar();
        
        // Render step content
        $this->renderStepContent();
        
        // Render navigation
        $this->renderNavigation();
        
        // Render debug info
        if ($this->debug) {
            $this->renderDebugInfo();
        }
        
        return ob_get_clean();
    }
    
    /**
     * Render CSS styles
     */
    /**
     * Render progress bar
     */
    private function renderProgressBar() {
        $percentage = ($this->currentStep / $this->totalSteps) * 100;
        
        echo '<div class="formstep-progress">';
        echo '<div class="formstep-progress-bar" style="width: ' . $percentage . '%"></div>';
        echo '<span class="formstep-progress-text">Bước ' . $this->currentStep . '/' . $this->totalSteps . '</span>';
        echo '</div>';
    }
    
    /**
     * Render step content
     */
    private function renderStepContent() {
        $stepConfig = $this->steps[$this->currentStep] ?? [];
        $loadView = $stepConfig['loadView'] ?? [];
        
        echo '<div class="formstep-content">';
        
        // Render step title
        if (isset($stepConfig['title'])) {
            echo '<h3 class="formstep-title">' . htmlspecialchars($stepConfig['title']) . '</h3>';
        }
        
        // Render errors
        if (!empty($this->errors)) {
            echo '<div class="formstep-errors">';
            foreach ($this->errors as $error) {
                echo '<p class="formstep-error">' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
        
        // Render view
        if (isset($loadView['loadView'])) {
            $viewFile = $loadView['loadView'];
            
            if ($loadView['isFunctionView'] ?? false) {
                // Function view
                if (is_callable($viewFile)) {
                    echo call_user_func($viewFile, $this->getStepData($this->currentStep), $this->currentStep);
                }
            } else {
                // File view
                if (file_exists($viewFile)) {
                    $stepData = $this->getStepData($this->currentStep);
                    $currentStep = $this->currentStep;
                    include $viewFile;
                }
            }
        }
        
        echo '</div>';
    }
    
    /**
     * Render navigation buttons
     */
    private function renderNavigation() {
        $stepConfig = $this->steps[$this->currentStep] ?? [];
        $buttonNext = $stepConfig['buttonNext'] ?? [];
        $buttonPrev = $stepConfig['buttonPrev'] ?? [];
        
        echo '<div class="formstep-navigation">';
        
        // Previous button
        if ($this->currentStep > 1) {
            $prevLabel = $buttonPrev['label'] ?? 'Trở lại';
            echo '<button type="button" class="formstep-btn formstep-btn-prev" onclick="FormStep.prev()">';
            echo htmlspecialchars($prevLabel);
            echo '</button>';
        }
        
        // Next/Submit button
        if ($this->currentStep < $this->totalSteps) {
            $nextLabel = $buttonNext['label'] ?? 'Tiếp tục';
            echo '<button type="button" class="formstep-btn formstep-btn-next" onclick="FormStep.next()">';
            echo htmlspecialchars($nextLabel);
            echo '</button>';
        } else {
            $submitLabel = $buttonNext['label'] ?? 'Hoàn thành';
            echo '<button type="button" class="formstep-btn formstep-btn-submit" onclick="FormStep.submit()">';
            echo htmlspecialchars($submitLabel);
            echo '</button>';
        }
        
        echo '</div>';
    }
    
    /**
     * Render JavaScript
     */
    /**
     * Render debug information
     */
    private function renderDebugInfo() {
        echo '<div class="formstep-debug">';
        echo '<h4>Debug Information</h4>';
        echo '<p><strong>Current Step:</strong> ' . $this->currentStep . '</p>';
        echo '<p><strong>Mode:</strong> ' . $this->mode . '</p>';
        echo '<p><strong>Total Steps:</strong> ' . $this->totalSteps . '</p>';
        echo '<p><strong>Session Data:</strong></p>';
        echo '<pre>' . htmlspecialchars(print_r($_SESSION, true)) . '</pre>';
        echo '</div>';
    }
    
    /**
     * Get current step
     */
    public function getCurrentStep() {
        return $this->currentStep;
    }
    
    /**
     * Get all step data
     */
    public function getAllStepData() {
        $allData = [];
        for ($i = 1; $i <= $this->totalSteps; $i++) {
            $allData[$i] = $this->getStepData($i);
        }
        return $allData;
    }
    
    /**
     * Reset form
     */
    public function reset() {
        for ($i = 1; $i <= $this->totalSteps; $i++) {
            $this->clearStepData($i);
        }
        unset($_SESSION[$this->sessionPrefix . 'current_step']);
        $this->currentStep = $this->config['initStep'] ?? 1;
    }
    
    /**
     * Get errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Check if form is completed
     */
    public function isCompleted() {
        return $this->currentStep >= $this->totalSteps;
    }
    
    /**
     * Get JavaScript configuration
     */
    public function getJavaScriptConfig() {
        // Prepare steps config for JavaScript (remove PHP functions)
        $jsSteps = [];
        foreach ($this->steps as $stepNum => $stepConfig) {
            $jsSteps[$stepNum] = [
                'title' => $stepConfig['title'] ?? '',
                'buttonNext' => [
                    'label' => $stepConfig['buttonNext']['label'] ?? 'Next',
                    'submitBeforeContinue' => $stepConfig['buttonNext']['submitBeforeContinue'] ?? false,
                    'required' => $stepConfig['buttonNext']['required'] ?? false,
                    // Note: PHP validation functions are not included in JS config
                ],
                'buttonPrev' => [
                    'label' => $stepConfig['buttonPrev']['label'] ?? 'Previous',
                    'clearInput' => $stepConfig['buttonPrev']['clearInput'] ?? false
                ],
                'handleSubmit' => [
                    'URLhandle' => $stepConfig['handleSubmit']['URLhandle'] ?? '',
                    'inputKeyForForm' => $stepConfig['handleSubmit']['inputKeyForForm'] ?? ''
                ]
            ];
        }
        
        return json_encode([
            'currentStep' => $this->currentStep,
            'totalSteps' => $this->totalSteps,
            'mode' => $this->mode,
            'steps' => $jsSteps,
            'sessionPrefix' => $this->sessionPrefix
        ]);
    }
}
