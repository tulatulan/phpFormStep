/**
 * phpFormStep - Professional JavaScript Functions
 * 
 * Handles form navigation, validation, and AJAX submissions
 */

window.FormStep = (function() {
    'use strict';
    
    // Private variables
    let config = window.FormStepConfig || {};
    let isProcessing = false;
    let validationCallbacks = {};
    
    // Initialize
    function init() {
        setupEventListeners();
        setupFormValidation();
        addAnimations();
        
        // Auto-save feature
        if (config.autoSave) {
            setupAutoSave();
        }
    }
    
    // Setup event listeners
    function setupEventListeners() {
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission prevention
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    return false;
                });
            });
            
            // Input validation on change
            const inputs = document.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    validateField(this);
                });
                
                input.addEventListener('blur', function() {
                    validateField(this);
                });
            });
            
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 'ArrowRight':
                            e.preventDefault();
                            next();
                            break;
                        case 'ArrowLeft':
                            e.preventDefault();
                            prev();
                            break;
                        case 'Enter':
                            if (e.shiftKey) {
                                e.preventDefault();
                                submit();
                            }
                            break;
                    }
                }
            });
        });
    }
    
    // Setup form validation
    function setupFormValidation() {
        // Add validation classes
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            if (input.hasAttribute('required')) {
                input.classList.add('formstep-required');
            }
        });
    }
    
    // Add animations
    function addAnimations() {
        const content = document.querySelector('.formstep-content');
        if (content) {
            content.classList.add('formstep-fade-in');
        }
    }
    
    // Validate individual field
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
        errorElement.style.color = '#dc3545';
        errorElement.style.fontSize = '12px';
        errorElement.style.marginTop = '5px';
        
        field.parentNode.insertBefore(errorElement, field.nextSibling);
    }
    
    // Remove field error
    function removeFieldError(field) {
        const errorElement = field.parentNode.querySelector('.formstep-field-error');
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
    
    // Save step data
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
        
        formData.append('formstep_action', 'save');
        formData.append('current_step', config.currentStep);
        
        // Save to localStorage as backup
        const dataObj = {};
        for (let [key, value] of formData.entries()) {
            dataObj[key] = value;
        }
        localStorage.setItem('formstep_backup_' + config.currentStep, JSON.stringify(dataObj));
    }
    
    // Load step data
    function loadStepData() {
        const saved = localStorage.getItem('formstep_backup_' + config.currentStep);
        if (saved) {
            const data = JSON.parse(saved);
            
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
        }
    }
    
    // Show loading state
    function showLoading() {
        const content = document.querySelector('.formstep-content');
        if (content) {
            content.classList.add('formstep-loading');
        }
        
        const buttons = document.querySelectorAll('.formstep-btn');
        buttons.forEach(btn => {
            btn.disabled = true;
        });
    }
    
    // Hide loading state
    function hideLoading() {
        const content = document.querySelector('.formstep-content');
        if (content) {
            content.classList.remove('formstep-loading');
        }
        
        const buttons = document.querySelectorAll('.formstep-btn');
        buttons.forEach(btn => {
            btn.disabled = false;
        });
    }
    
    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `formstep-notification formstep-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 6px;
            color: white;
            z-index: 10000;
            animation: formstep-slide-in 0.3s ease;
        `;
        
        switch(type) {
            case 'success':
                notification.style.backgroundColor = '#28a745';
                break;
            case 'error':
                notification.style.backgroundColor = '#dc3545';
                break;
            case 'warning':
                notification.style.backgroundColor = '#ffc107';
                notification.style.color = '#212529';
                break;
            default:
                notification.style.backgroundColor = '#007bff';
        }
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Submit form
    function submitForm(action) {
        if (isProcessing) return;
        
        isProcessing = true;
        showLoading();
        
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
        
        formData.append('formstep_action', action);
        formData.append('current_step', config.currentStep);
        
        // Submit via page reload (traditional form)
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        for (let [key, value] of formData.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }
        
        document.body.appendChild(form);
        form.submit();
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
            
            // Custom validation
            if (buttonNext.validate) {
                const result = buttonNext.validate();
                if (result !== true) {
                    showNotification(result || 'Validation failed', 'error');
                    return;
                }
            }
            
            submitForm('next');
        },
        
        prev: function() {
            if (isProcessing) return;
            submitForm('prev');
        },
        
        goto: function(step) {
            if (isProcessing) return;
            
            const formData = new FormData();
            formData.append('formstep_action', 'goto');
            formData.append('target_step', step);
            
            submitForm('goto');
        },
        
        submit: function() {
            if (isProcessing) return;
            
            if (!validateCurrentStep()) {
                showNotification('Please fix the errors before submitting', 'error');
                return;
            }
            
            submitForm('submit');
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
