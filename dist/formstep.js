/**
 * phpFormStep - Core JavaScript Library
 * 
 * Core functionality for form step navigation and processing.
 * This is the essential JavaScript that works with the PHP library.
 * 
 * @version 2.0.0
 * @author ChatFree Team
 * @license MIT
 */

window.FormStep = (function() {
    'use strict';
    
    let config = {};
    let isProcessing = false;
    let validationCallbacks = {};
    
    // Initialize the form step
    function init() {
        // Get configuration from window
        if (window.FormStepConfig) {
            config = window.FormStepConfig;
        }
        
        // Setup event listeners
        setupEventListeners();
        
        // Setup auto-save
        setupAutoSave();
        
        // Load saved data
        loadStepData();
        
        console.log('FormStep initialized with config:', config);
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Form submit prevention
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.classList.contains('formstep-form')) {
                e.preventDefault();
            }
        });
        
        // Input validation
        document.addEventListener('input', function(e) {
            if (e.target.matches('input, textarea, select')) {
                validateField(e.target);
            }
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    prev();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    next();
                }
            }
        });
    }
    
    // Validate field
    function validateField(field) {
        const fieldName = field.name;
        const fieldValue = field.value;
        let isValid = true;
        let errorMessage = '';
        
        // Remove existing error styles
        field.classList.remove('formstep-error');
        removeFieldError(field);
        
        // Required field validation
        if (field.hasAttribute('required') && !fieldValue.trim()) {
            isValid = false;
            errorMessage = 'This field is required';
        }
        
        // Email validation
        if (field.type === 'email' && fieldValue) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(fieldValue)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
        }
        
        // Phone validation
        if (field.type === 'tel' && fieldValue) {
            const phoneRegex = /^[\d\s\-\+\(\)]+$/;
            if (!phoneRegex.test(fieldValue)) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number';
            }
        }
        
        // Custom validation
        if (validationCallbacks[fieldName]) {
            const customResult = validationCallbacks[fieldName](fieldValue, field);
            if (customResult !== true) {
                isValid = false;
                errorMessage = customResult || 'Invalid input';
            }
        }
        
        // Apply error styles
        if (!isValid) {
            field.classList.add('formstep-error');
            showFieldError(field, errorMessage);
        }
        
        return isValid;
    }
    
    // Show field error
    function showFieldError(field, message) {
        const errorElement = document.createElement('div');
        errorElement.className = 'formstep-field-error';
        errorElement.textContent = message;
        
        const parent = field.parentElement;
        parent.appendChild(errorElement);
        
        // Position error element
        errorElement.style.display = 'block';
    }
    
    // Remove field error
    function removeFieldError(field) {
        const parent = field.parentElement;
        const errorElement = parent.querySelector('.formstep-field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    // Validate current step
    function validateCurrentStep() {
        const inputs = document.querySelectorAll('input, textarea, select');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    // Setup auto-save
    function setupAutoSave() {
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', debounce(function() {
                saveStepData();
            }, 1000));
        });
    }
    
    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Save step data to localStorage
    function saveStepData() {
        const formData = new FormData();
        const inputs = document.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            if (input.type === 'checkbox' || input.type === 'radio') {
                if (input.checked) {
                    formData.append(input.name, input.value);
                }
            } else {
                formData.append(input.name, input.value);
            }
        });
        
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        localStorage.setItem(`formstep_${config.sessionPrefix}_step_${config.currentStep}`, JSON.stringify(data));
    }
    
    // Load step data from localStorage
    function loadStepData() {
        const savedData = localStorage.getItem(`formstep_${config.sessionPrefix}_step_${config.currentStep}`);
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                
                Object.keys(data).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            input.checked = (input.value === data[key]);
                        } else {
                            input.value = data[key];
                        }
                    }
                });
            } catch (e) {
                console.error('Error loading step data:', e);
            }
        }
    }
    
    // Submit form
    function submitForm(action) {
        if (isProcessing) return;
        
        isProcessing = true;
        showLoading();
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        // Add action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'formstep_action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        // Add all form data
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            const clone = input.cloneNode(true);
            if (input.type === 'checkbox' || input.type === 'radio') {
                clone.checked = input.checked;
            }
            form.appendChild(clone);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
    
    // Show loading
    function showLoading() {
        const loader = document.createElement('div');
        loader.className = 'formstep-loader';
        loader.innerHTML = '<div class="formstep-spinner"></div>';
        document.body.appendChild(loader);
    }
    
    // Hide loading
    function hideLoading() {
        const loader = document.querySelector('.formstep-loader');
        if (loader) {
            loader.remove();
        }
        isProcessing = false;
    }
    
    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `formstep-notification formstep-notification-${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Hide after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Public API
    return {
        // Initialize the form step
        init: init,
        
        // Navigation functions
        next: function() {
            if (isProcessing) return;
            
            const stepConfig = config.steps[config.currentStep];
            const buttonNext = stepConfig?.buttonNext || {};
            
            // Validate if required
            if (buttonNext.required && !validateCurrentStep()) {
                showNotification('Please fix the errors before continuing', 'error');
                return;
            }
            
            // Note: PHP validation functions are handled server-side
            // Client-side validation should be implemented separately if needed
            
            submitForm('next');
        },
        
        prev: function() {
            if (isProcessing) return;
            submitForm('prev');
        },
        
        submit: function() {
            if (isProcessing) return;
            
            if (!validateCurrentStep()) {
                showNotification('Please fix the errors before submitting', 'error');
                return;
            }
            
            submitForm('submit');
        },
        
        // Go to specific step
        goToStep: function(stepNumber) {
            if (isProcessing) return;
            
            // Validate step number
            if (stepNumber < 1 || stepNumber > config.totalSteps) {
                showNotification('Invalid step number', 'error');
                return;
            }
            
            // Can only go to current or previous steps (no skipping ahead)
            if (stepNumber > config.currentStep) {
                showNotification('Cannot skip ahead to future steps', 'error');
                return;
            }
            
            // If going to current step, just do nothing
            if (stepNumber === config.currentStep) {
                return;
            }
            
            // Go to previous step
            window.location.href = `?step=${stepNumber}`;
        },
        
        // Validation functions
        addValidation: function(fieldName, callback) {
            validationCallbacks[fieldName] = callback;
        },
        
        validate: validateCurrentStep,
        
        // Data functions
        saveData: saveStepData,
        loadData: loadStepData,
        
        // Utility functions
        showNotification: showNotification,
        showLoading: showLoading,
        hideLoading: hideLoading,
        
        // Get current configuration
        getConfig: function() {
            return config;
        }
    };
})();

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', FormStep.init);
} else {
    FormStep.init();
}
