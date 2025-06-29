<?php
/**
 * phpFormStep - Professional Multi-Step Form Library
 * Validation Class
 */

namespace phpFormStep;

// Library protection
if (!defined('PHPFORMSTEP_LOADED') && php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Direct access to phpFormStep library files is forbidden. Use the bootstrap.php entry point.');
}

class FormStepValidator {
    
    private array $rules = [];
    private array $errors = [];
    
    /**
     * Set validation rules for a step
     */
    public function setRules(string $step, array $rules): void {
        $this->rules[$step] = $rules;
    }
    
    /**
     * Validate step data
     */
    public function validate(string $step, array $data): bool {
        $this->errors = [];
        
        if (!isset($this->rules[$step])) {
            return true; // No rules defined, assume valid
        }
        
        foreach ($this->rules[$step] as $field => $rules) {
            // Convert string rules to array
            if (is_string($rules)) {
                $rules = explode('|', $rules);
            }
            
            $this->validateField($field, $data[$field] ?? null, $rules);
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate individual field
     */
    private function validateField(string $field, $value, array $rules): void {
        foreach ($rules as $rule) {
            if (is_string($rule)) {
                $this->applyRule($field, $value, $rule);
            } elseif (is_array($rule) && isset($rule['rule'])) {
                $this->applyRule($field, $value, $rule['rule'], $rule['message'] ?? null);
            }
        }
    }
    
    /**
     * Apply validation rule
     */
    private function applyRule(string $field, $value, string $rule, ?string $customMessage = null): void {
        $valid = true;
        $message = $customMessage;
        
        switch ($rule) {
            case 'required':
                $valid = !empty($value) || $value === '0';
                $message = $message ?: "The {$field} field is required.";
                break;
                
            case 'email':
                $valid = empty($value) || filter_var($value, FILTER_VALIDATE_EMAIL);
                $message = $message ?: "The {$field} field must be a valid email address.";
                break;
                
            case 'numeric':
                $valid = empty($value) || is_numeric($value);
                $message = $message ?: "The {$field} field must be numeric.";
                break;
                
            case 'url':
                $valid = empty($value) || filter_var($value, FILTER_VALIDATE_URL);
                $message = $message ?: "The {$field} field must be a valid URL.";
                break;
                
            default:
                // Handle min/max length rules
                if (preg_match('/^min:(\d+)$/', $rule, $matches)) {
                    $min = (int)$matches[1];
                    $valid = empty($value) || strlen($value) >= $min;
                    $message = $message ?: "The {$field} field must be at least {$min} characters.";
                } elseif (preg_match('/^max:(\d+)$/', $rule, $matches)) {
                    $max = (int)$matches[1];
                    $valid = empty($value) || strlen($value) <= $max;
                    $message = $message ?: "The {$field} field must not exceed {$max} characters.";
                } elseif (preg_match('/^min_value:(\d+)$/', $rule, $matches)) {
                    $min = (int)$matches[1];
                    $valid = empty($value) || (is_numeric($value) && $value >= $min);
                    $message = $message ?: "The {$field} field must be at least {$min}.";
                } elseif (preg_match('/^max_value:(\d+)$/', $rule, $matches)) {
                    $max = (int)$matches[1];
                    $valid = empty($value) || (is_numeric($value) && $value <= $max);
                    $message = $message ?: "The {$field} field must not exceed {$max}.";
                }
                break;
        }
        
        if (!$valid) {
            $this->errors[$field][] = $message;
        }
    }
    
    /**
     * Get validation errors
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Get errors for specific field
     */
    public function getFieldErrors(string $field): array {
        return $this->errors[$field] ?? [];
    }
    
    /**
     * Check if field has errors
     */
    public function hasFieldErrors(string $field): bool {
        return !empty($this->errors[$field]);
    }
    
    /**
     * Clear all errors
     */
    public function clearErrors(): void {
        $this->errors = [];
    }
    
    /**
     * Add custom error
     */
    public function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }
    
    /**
     * Get first error for field
     */
    public function getFirstFieldError(string $field): ?string {
        $errors = $this->getFieldErrors($field);
        return $errors[0] ?? null;
    }
    
    /**
     * Get all errors as flat array
     */
    public function getAllErrors(): array {
        $allErrors = [];
        foreach ($this->errors as $fieldErrors) {
            $allErrors = array_merge($allErrors, $fieldErrors);
        }
        return $allErrors;
    }
}