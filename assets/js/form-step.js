/**
 * phpFormStep Library - JavaScript
 * Client-side functionality for multi-step forms
 */

class FormStepManager {
    constructor(options = {}) {
        this.config = {
            formId: 'multi-step-form',
            totalSteps: 5,
            currentStep: 1,
            enableAjax: true,
            holdSteps: [],
            submitUrl: '',
            completedSteps: [],
            ...options
        };
        
        this.form = null;
        this.steps = [];
        this.currentStepElement = null;
        this.events = {};
        
        this.init();
    }
    
    /**
     * Initialize the form step manager
     */
    init() {
        this.form = document.getElementById(this.config.formId);
        if (!this.form) {
            console.error('Form element not found:', this.config.formId);
            return;
        }
        
        // Load config from data attribute if available
        const dataConfig = this.form.querySelector('[data-form-step-config]');
        if (dataConfig) {
            try {
                const config = JSON.parse(dataConfig.getAttribute('data-form-step-config'));
                this.config = { ...this.config, ...config };
            } catch (e) {
                console.warn('Failed to parse form step config from data attribute');
            }
        }
        
        this.setupSteps();
        this.setupNavigation();
        this.setupEventListeners();
        this.setupUrlRouting();
        
        // Show initial step
        this.showStep(this.config.currentStep);
        
        console.log('FormStepManager initialized:', this.config);
    }
    
    /**
     * Setup step elements
     */
    setupSteps() {
        this.steps = Array.from(this.form.querySelectorAll('.step-content'));
        this.updateProgressIndicator();
    }
    
    /**
     * Setup navigation event listeners
     */
    setupNavigation() {
        // Step navigation from progress indicator
        const stepItems = document.querySelectorAll('.step-item[data-step]');
        stepItems.forEach(item => {
            if (item.style.cursor === 'pointer') {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    const step = parseInt(item.getAttribute('data-step'));
                    this.goToStep(step);
                });
            }
        });
        
        // Previous/Next buttons
        const prevBtn = this.form.querySelector('#btn-previous');
        const nextBtn = this.form.querySelector('#btn-next');
        const submitBtn = this.form.querySelector('#btn-submit');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.previousStep();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.nextStep();
            });
        }
        
        if (submitBtn) {
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.submitForm();
            });
        }
    }
    
    /**
     * Setup additional event listeners
     */
    setupEventListeners() {
        // Form validation on input
        this.form.addEventListener('input', (e) => {
            this.validateField(e.target);
        });
        
        // Handle form submission
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm();
        });
        
        // Handle browser back/forward
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.step) {
                this.showStep(e.state.step, false);
            }
        });
    }
    
    /**
     * Setup URL routing
     */
    setupUrlRouting() {
        const urlParams = new URLSearchParams(window.location.search);
        const stepParam = urlParams.get('step');
        
        if (stepParam) {
            const step = parseInt(stepParam);
            if (this.isValidStep(step) && this.canAccessStep(step)) {
                this.config.currentStep = step;
            }
        }
    }
    
    /**
     * Show specific step
     */
    showStep(step, pushState = true) {
        if (!this.isValidStep(step) || !this.canAccessStep(step)) {
            console.warn('Cannot access step:', step);
            return false;
        }
        
        // Hide all steps
        this.steps.forEach((stepEl, index) => {
            stepEl.classList.remove('active', 'show');
        });
        
        // Show target step
        const targetStep = this.form.querySelector(`#step${step}`);
        if (targetStep) {
            targetStep.classList.add('active', 'show');
            this.currentStepElement = targetStep;
        }
        
        // Update progress indicator
        this.updateProgressIndicator(step);
        
        // Update navigation buttons
        this.updateNavigationButtons(step);
        
        // Update URL
        if (pushState) {
            const url = new URL(window.location);
            url.searchParams.set('step', step);
            history.pushState({ step }, '', url);
        }
        
        // Update current step
        this.config.currentStep = step;
        
        // Trigger event
        this.trigger('stepChange', { step, element: targetStep });
        
        console.log('Showing step:', step);
        return true;
    }
    
    /**
     * Go to next step
     */
    nextStep() {
        if (!this.validateCurrentStep()) {
            console.warn('Current step validation failed');
            return false;
        }
        
        const nextStep = this.config.currentStep + 1;
        if (nextStep <= this.config.totalSteps) {
            if (this.config.enableAjax) {
                this.saveCurrentStep().then(() => {
                    this.markStepCompleted(this.config.currentStep);
                    this.showStep(nextStep);
                });
            } else {
                this.markStepCompleted(this.config.currentStep);
                this.showStep(nextStep);
            }
        }
        return true;
    }
    
    /**
     * Go to previous step
     */
    previousStep() {
        const prevStep = this.config.currentStep - 1;
        if (prevStep >= 1) {
            this.showStep(prevStep);
        }
    }
    
    /**
     * Go to specific step
     */
    goToStep(step) {
        return this.showStep(step);
    }
    
    /**
     * Check if step number is valid
     */
    isValidStep(step) {
        return step >= 1 && step <= this.config.totalSteps;
    }
    
    /**
     * Check if user can access step
     */
    canAccessStep(step) {
        // Always allow step 1
        if (step === 1) return true;
        
        // Check hold steps
        if (this.config.holdSteps.includes(step)) {
            for (let i = 1; i < step; i++) {
                if (!this.config.completedSteps.includes(i)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Mark step as completed
     */
    markStepCompleted(step) {
        if (!this.config.completedSteps.includes(step)) {
            this.config.completedSteps.push(step);
            this.updateProgressIndicator();
            this.trigger('stepComplete', { step });
        }
    }
    
    /**
     * Validate current step
     */
    validateCurrentStep() {
        if (!this.currentStepElement) return false;
        
        const requiredFields = this.currentStepElement.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        // Custom validation
        const result = this.trigger('stepValidate', { 
            step: this.config.currentStep, 
            element: this.currentStepElement,
            isValid 
        });
        
        if (result.some(r => r === false)) {
            isValid = false;
        }
        
        return isValid;
    }
    
    /**
     * Validate individual field
     */
    validateField(field) {
        if (!field.name) return true;
        
        let isValid = true;
        let message = '';
        
        // Required field validation
        if (field.hasAttribute('required') && !field.value.trim()) {
            isValid = false;
            message = 'This field is required';
        }
        
        // Email validation
        if (field.type === 'email' && field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
            isValid = false;
            message = 'Please enter a valid email address';
        }
        
        // Apply validation styles
        field.classList.remove('is-valid', 'is-invalid');
        if (isValid) {
            field.classList.add('is-valid');
            this.hideFeedback(field);
        } else {
            field.classList.add('is-invalid');
            this.showFeedback(field, message);
        }
        
        return isValid;
    }
    
    /**
     * Show field validation feedback
     */
    showFeedback(field, message) {
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        
        feedback.textContent = message;
    }
    
    /**
     * Hide field validation feedback
     */
    hideFeedback(field) {
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }
    
    /**
     * Save current step data via AJAX
     */
    async saveCurrentStep() {
        if (!this.config.enableAjax || !this.config.submitUrl) {
            return Promise.resolve();
        }
        
        const formData = new FormData();
        formData.append('action', 'save_step');
        formData.append('step', this.config.currentStep);
        
        // Collect step data
        const stepFields = this.currentStepElement.querySelectorAll('input, textarea, select');
        stepFields.forEach(field => {
            if (field.name) {
                if (field.type === 'file' && field.files.length > 0) {
                    for (let file of field.files) {
                        formData.append(field.name, file);
                    }
                } else if (field.type === 'checkbox' || field.type === 'radio') {
                    if (field.checked) {
                        formData.append(field.name, field.value);
                    }
                } else {
                    formData.append(field.name, field.value);
                }
            }
        });
        
        try {
            this.showLoading(true);
            
            const response = await fetch(this.config.submitUrl, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage('Step saved successfully!', 'success');
                this.trigger('stepSaved', { step: this.config.currentStep, data });
            } else {
                this.showMessage(data.message || 'Failed to save step', 'error');
                throw new Error(data.message || 'Save failed');
            }
            
            return data;
            
        } catch (error) {
            console.error('Save step error:', error);
            this.showMessage('Connection error: ' + error.message, 'error');
            throw error;
            
        } finally {
            this.showLoading(false);
        }
    }
    
    /**
     * Submit complete form
     */
    async submitForm() {
        if (!this.validateCurrentStep()) {
            this.showMessage('Please complete all required fields', 'error');
            return;
        }
        
        if (this.config.enableAjax) {
            await this.saveCurrentStep();
        }
        
        // Mark final step as completed
        this.markStepCompleted(this.config.currentStep);
        
        // Collect all form data
        const formData = new FormData(this.form);
        formData.append('action', 'submit_form');
        
        if (this.config.enableAjax && this.config.submitUrl) {
            try {
                this.showLoading(true);
                
                const response = await fetch(this.config.submitUrl, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showMessage('Form submitted successfully!', 'success');
                    this.trigger('formComplete', { data });
                    
                    // Redirect if specified
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    }
                } else {
                    this.showMessage(data.message || 'Submission failed', 'error');
                }
                
            } catch (error) {
                console.error('Submit error:', error);
                this.showMessage('Connection error: ' + error.message, 'error');
            } finally {
                this.showLoading(false);
            }
        } else {
            // Non-AJAX submission
            this.form.submit();
        }
    }
    
    /**
     * Update progress indicator
     */
    updateProgressIndicator(currentStep = null) {
        currentStep = currentStep || this.config.currentStep;
        
        const stepItems = document.querySelectorAll('.step-item[data-step]');
        stepItems.forEach(item => {
            const step = parseInt(item.getAttribute('data-step'));
            
            item.classList.remove('active', 'completed');
            
            if (step === currentStep) {
                item.classList.add('active');
            } else if (this.config.completedSteps.includes(step)) {
                item.classList.add('completed');
            }
        });
    }
    
    /**
     * Update navigation buttons
     */
    updateNavigationButtons(step) {
        const prevBtn = this.form.querySelector('#btn-previous');
        const nextBtn = this.form.querySelector('#btn-next');
        const submitBtn = this.form.querySelector('#btn-submit');
        
        // Previous button
        if (prevBtn) {
            prevBtn.style.display = step > 1 ? 'inline-flex' : 'none';
            prevBtn.setAttribute('data-step', step - 1);
        }
        
        // Next/Submit buttons
        if (step < this.config.totalSteps) {
            if (nextBtn) {
                nextBtn.style.display = 'inline-flex';
                nextBtn.setAttribute('data-step', step + 1);
            }
            if (submitBtn) {
                submitBtn.style.display = 'none';
            }
        } else {
            if (nextBtn) {
                nextBtn.style.display = 'none';
            }
            if (submitBtn) {
                submitBtn.style.display = 'inline-flex';
            }
        }
    }
    
    /**
     * Show loading state
     */
    showLoading(show) {
        if (show) {
            this.form.classList.add('step-loading');
        } else {
            this.form.classList.remove('step-loading');
        }
    }
    
    /**
     * Show message to user
     */
    showMessage(message, type = 'info') {
        // Remove existing messages
        document.querySelectorAll('.step-message').forEach(msg => msg.remove());
        
        // Create new message
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show step-message`;
        alertDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    /**
     * Event system
     */
    on(event, callback) {
        if (!this.events[event]) {
            this.events[event] = [];
        }
        this.events[event].push(callback);
    }
    
    trigger(event, data = {}) {
        if (!this.events[event]) return [];
        
        return this.events[event].map(callback => {
            try {
                return callback(data);
            } catch (error) {
                console.error('Event callback error:', error);
                return null;
            }
        });
    }
    
    /**
     * Get current step data
     */
    getCurrentStepData() {
        if (!this.currentStepElement) return {};
        
        const data = {};
        const fields = this.currentStepElement.querySelectorAll('input, textarea, select');
        
        fields.forEach(field => {
            if (field.name) {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    if (field.checked) {
                        data[field.name] = field.value;
                    }
                } else {
                    data[field.name] = field.value;
                }
            }
        });
        
        return data;
    }
    
    /**
     * Set step data
     */
    setStepData(step, data) {
        const stepElement = this.form.querySelector(`#step${step}`);
        if (!stepElement) return;
        
        Object.keys(data).forEach(name => {
            const field = stepElement.querySelector(`[name="${name}"]`);
            if (field) {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = field.value === data[name];
                } else {
                    field.value = data[name];
                }
            }
        });
    }
    
    /**
     * Reset form
     */
    reset() {
        this.config.currentStep = 1;
        this.config.completedSteps = [];
        this.form.reset();
        this.showStep(1);
        this.updateProgressIndicator();
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Look for forms with form-step configuration
    const forms = document.querySelectorAll('[data-form-step-config]');
    
    forms.forEach(form => {
        try {
            const config = JSON.parse(form.getAttribute('data-form-step-config'));
            new FormStepManager(config);
        } catch (error) {
            console.error('Failed to initialize FormStepManager:', error);
        }
    });
    
    // Initialize default form if exists
    const defaultForm = document.getElementById('multi-step-form');
    if (defaultForm && !defaultForm.hasAttribute('data-form-step-config')) {
        new FormStepManager();
    }
});

// Global export
window.FormStepManager = FormStepManager;
