# phpFormStep - Professional Multi-Step Form Library for PHP

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/Version-1.0.0-orange.svg)](VERSION)

A robust, professional PHP library for creating multi-step forms with advanced configuration, validation, and state management.

## 🚀 Features

- **🔧 Advanced Configuration** - Total steps, init step, required save steps, navigation control
- **📁 Flexible Step Management** - Per-step file/handler mapping, create/edit mode support  
- **✅ Built-in Validation** - Laravel-style validation rules with custom error messages
- **💾 Session-based State** - Secure state management with session storage
- **👨‍💻 Developer Friendly** - Clean OOP architecture, easy integration, comprehensive documentation
- **🛡️ Protected Core** - Bootstrap system prevents direct file access
- **🎯 Production Ready** - Enterprise-grade code quality and error handling

## 📦 Installation

### Method 1: Direct Download

```bash
# Clone the repository
git clone https://github.com/tulatulan/phpFormStep.git
cd phpFormStep

# Include in your project
require_once 'phpFormStep/bootstrap.php';
```

### Method 2: Composer (Recommended)

```bash
# Install via Composer
composer require tulatulan/phpformstep
```

```php
<?php
require_once 'vendor/autoload.php';
use phpFormStep\FormStepConfig;
use phpFormStep\FormStepManager;
```

## 📦 Cấu trúc thư viện

```
lib/phpFormStep/src/
├── FormStepConfig.php     ← Cấu hình form
├── FormStepState.php      ← Quản lý trạng thái session
├── FormStepValidator.php  ← Validation engine
└── FormStepManager.php    ← Main controller
```

## 🚀 Cách sử dụng

### 1. Include thư viện

```php
<?php
require_once 'lib/phpFormStep/src/FormStepConfig.php';
require_once 'lib/phpFormStep/src/FormStepState.php';
require_once 'lib/phpFormStep/src/FormStepValidator.php';
require_once 'lib/phpFormStep/src/FormStepManager.php';

use phpFormStep\FormStepConfig;
use phpFormStep\FormStepManager;
```

### 2. Cấu hình form

```php
// Tạo cấu hình
$config = new FormStepConfig([
    'totalSteps' => 4,                  // Tổng số step
    'initStep' => 1,                    // Step khởi tạo (tạo record)
    'requiredSaveSteps' => [1, 3],      // Step bắt buộc save
    'allowNavigation' => true,          // Cho phép chuyển step
    'mode' => 'create',                 // 'create' hoặc 'edit'
    'sessionPrefix' => 'my_form_',
    'tableName' => 'articles'           // Tên bảng (optional)
]);
```

### 3. Mapping step với files

```php
// Mapping step với file PHP
$config->setStepFile(1, 'views/step1-basic.php');
$config->setStepFile(2, 'views/step2-details.php');
$config->setStepFile(3, 'views/step3-content.php');
$config->setStepFile(4, 'views/step4-review.php');
```

### 4. Mapping step với handlers

```php
// Handler cho step 1 - Step khởi tạo (tạo record)
$config->setStepHandler(1, function($data, $state) {
    if (empty($data['title'])) {
        return ['success' => false, 'errors' => ['Title is required']];
    }
    
    // Tạo record trong database
    $id = ArticleModel::create([
        'title' => $data['title'],
        'status' => 'draft'
    ]);
    
    // Lưu primary key vào state
    $state->setPrimaryKeyValue($id);
    $state->markAsInitialized();
    
    return ['success' => true, 'message' => 'Article created'];
});

// Handler cho step 3 - Step bắt buộc save
$config->setStepHandler(3, function($data, $state) {
    if (empty($data['content'])) {
        return ['success' => false, 'errors' => ['Content is required']];
    }
    
    $articleId = $state->getPrimaryKeyValue();
    ArticleModel::update($articleId, ['content' => $data['content']]);
    
    return ['success' => true, 'message' => 'Content saved'];
});
```

### 5. Validation rules

```php
// Validation cho từng step
$config->setValidationRules(1, [
    'title' => 'required|min:5|max:100',
    'category' => 'required'
]);

$config->setValidationRules(3, [
    'content' => 'required|min:20'
]);
```

### 6. Khởi tạo và sử dụng

```php
// Khởi tạo form manager
$formManager = new FormStepManager($config, 'my_form');

// Lấy thông tin hiện tại
$currentStep = $formManager->getCurrentStep();
$stepData = $formManager->getStepData($currentStep);
$errors = $formManager->getErrors();
$isComplete = $formManager->isComplete();

// Xử lý hoàn thành
if ($isComplete) {
    $primaryKey = $formManager->getPrimaryKeyValue();
    $allData = $formManager->getAllStepData();
    
    // Redirect hoặc xử lý tiếp
    header('Location: success.php?id=' . $primaryKey);
    exit;
}
```

### 7. HTML Form

```html
<form method="POST">
    <div class="card">
        <div class="card-body">
            <!-- Render step hiện tại -->
            <?= $formManager->renderStep($currentStep) ?>
        </div>
        
        <div class="card-footer">
            <!-- Previous button -->
            <?php if ($currentStep > 1): ?>
                <button type="submit" name="action" value="prev" class="btn btn-secondary">
                    Previous
                </button>
            <?php endif; ?>
            
            <!-- Save button -->
            <button type="submit" name="action" value="save" class="btn btn-outline-primary">
                Save
            </button>
            
            <!-- Next/Complete button -->
            <?php if ($currentStep < $config->totalSteps): ?>
                <button type="submit" name="action" value="next" class="btn btn-primary">
                    Next
                </button>
            <?php else: ?>
                <button type="submit" name="action" value="complete" class="btn btn-success">
                    Complete
                </button>
            <?php endif; ?>
        </div>
    </div>
</form>
```

## 🔧 Edit Mode

Để sử dụng cho chỉnh sửa:

```php
$config = new FormStepConfig([
    'totalSteps' => 4,
    'initStep' => 1,
    'mode' => 'edit',                   // Chế độ edit
    'primaryKeyValue' => $articleId     // ID của record cần edit
]);

// Override method để load dữ liệu có sẵn
class MyFormStepManager extends FormStepManager {
    protected function loadExistingData(): void {
        $article = ArticleModel::find($this->config->primaryKeyValue);
        
        $this->state->setStepData(1, [
            'title' => $article->title,
            'category' => $article->category
        ]);
        
        $this->state->setStepData(3, [
            'content' => $article->content
        ]);
    }
}
```

## 📋 Form Actions

Thư viện tự động xử lý các action:

- `action=next` - Lưu và chuyển step tiếp theo
- `action=prev` - Quay lại step trước (nếu allowNavigation=true)
- `action=save` - Lưu step hiện tại (không chuyển step)
- `action=goto&target_step=3` - Chuyển đến step cụ thể
- `action=complete` - Hoàn thành form (chỉ ở step cuối)

## 🎯 Các khái niệm quan trọng

### Step khởi tạo (Init Step)
- Step tạo record chính trong database
- Phải được hoàn thành trước khi có thể di chuyển
- Tạo primary key cho các step khác sử dụng

### Required Save Steps
- Step bắt buộc phải save trước khi có thể next
- Không thể bỏ qua step này
- Thích hợp cho dữ liệu quan trọng

### Navigation Control
- `allowNavigation=true`: Có thể tự do di chuyển giữa steps
- `allowNavigation=false`: Chỉ có thể đi tuần tự

## 💡 Best Practices

1. **Luôn set initStep**: Step tạo record chính
2. **Sử dụng required save**: Cho dữ liệu quan trọng
3. **Validation rules**: Đặt validation cho từng step
4. **Handler functions**: Xử lý logic business cho mỗi step
5. **Error handling**: Luôn check và hiển thị errors

## 📚 Ví dụ hoàn chỉnh

Xem file `examples/professional-demo.php` để có ví dụ đầy đủ.

---

**Thư viện này được thiết kế để xử lý các form phức tạp với nhiều bước, phù hợp cho các ứng dụng enterprise.** 🚀

### 2. Khởi tạo và sử dụng

```php
// Tạo form với ID duy nhất
$form = new FormStep('my_form');

// Định nghĩa các bước - chỉ cần array tên bước
$form->setSteps([
    'Basic Information',
    'Contact Details',
    'Preferences', 
    'Review',
    'Complete'
]);

// Lấy thông tin hiện tại
$currentStep = $form->getCurrentStep();
$stepData = $form->getStepData($currentStep);
$isComplete = $form->isComplete();

// Xử lý khi hoàn thành
if ($isComplete) {
    $allData = $form->getAllData();
    // Lưu vào database hoặc xử lý
    echo "Form completed!";
}
```

### 3. HTML Form đơn giản

```html
<!-- Progress bar -->
<?= $form->renderProgress() ?>

<!-- Form -->
<form method="POST">
    <div class="card">
        <div class="card-body">
            <!-- Nội dung step hiện tại -->
            <?php if ($currentStep == 1): ?>
                <h4>Basic Information</h4>
                <input type="text" name="name" class="form-control" placeholder="Your name" 
                       value="<?= $stepData['name'] ?? '' ?>">
                <input type="email" name="email" class="form-control mt-2" placeholder="Your email"
                       value="<?= $stepData['email'] ?? '' ?>">
            
            <?php elseif ($currentStep == 2): ?>
                <h4>Contact Details</h4>
                <input type="text" name="phone" class="form-control" placeholder="Phone number"
                       value="<?= $stepData['phone'] ?? '' ?>">
                <textarea name="address" class="form-control mt-2" placeholder="Address"><?= $stepData['address'] ?? '' ?></textarea>
            
            <?php elseif ($currentStep == 3): ?>
                <h4>Preferences</h4>
                <select name="newsletter" class="form-control">
                    <option value="yes" <?= ($stepData['newsletter'] ?? '') == 'yes' ? 'selected' : '' ?>>Yes, send newsletter</option>
                    <option value="no" <?= ($stepData['newsletter'] ?? '') == 'no' ? 'selected' : '' ?>>No newsletter</option>
                </select>
            
            <?php elseif ($currentStep == 4): ?>
                <h4>Review Your Information</h4>
                <?php $allData = $form->getAllData(); ?>
                <p><strong>Name:</strong> <?= $allData[1]['name'] ?? 'Not provided' ?></p>
                <p><strong>Email:</strong> <?= $allData[1]['email'] ?? 'Not provided' ?></p>
                <p><strong>Phone:</strong> <?= $allData[2]['phone'] ?? 'Not provided' ?></p>
                <p><strong>Newsletter:</strong> <?= $allData[3]['newsletter'] ?? 'Not selected' ?></p>
            
            <?php else: ?>
                <h4>Thank You!</h4>
                <p>Your form has been submitted successfully.</p>
            <?php endif; ?>
        </div>
        
        <div class="card-footer">
            <!-- Navigation buttons -->
            <?= $form->renderNavigation() ?>
        </div>
    </div>
</form>

<!-- CSS Styles -->
<?= $form->renderCSS() ?>
```

## Ví dụ hoàn chỉnh

```php
<?php
session_start();
require_once 'lib/phpFormStep/src/FormStepSimple.php';
use phpFormStep\FormStep;

$form = new FormStep('contact_form');
$form->setSteps(['Personal Info', 'Contact Details', 'Review']);

$currentStep = $form->getCurrentStep();
$stepData = $form->getStepData();

if ($form->isComplete()) {
    // Xử lý dữ liệu hoàn thành
    $allData = $form->getAllData();
    // Lưu database, gửi email, etc.
    header('Location: success.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Multi-Step Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <?= $form->renderProgress() ?>
        
        <form method="POST">
            <!-- Nội dung step -->
            <!-- Navigation buttons -->
            <?= $form->renderNavigation() ?>
        </form>
        
        <?= $form->renderCSS() ?>
    </div>
</body>
</html>
```

## API Methods

### Khởi tạo
```php
$form = new FormStep('unique_form_id');
```

### Cấu hình steps
```php
$form->setSteps(['Step 1', 'Step 2', 'Step 3']);
```

### Lấy thông tin
```php
$currentStep = $form->getCurrentStep();        // Step hiện tại (1, 2, 3...)
$stepData = $form->getStepData();             // Dữ liệu step hiện tại
$stepData = $form->getStepData(2);            // Dữ liệu step cụ thể
$allData = $form->getAllData();               // Tất cả dữ liệu
$isComplete = $form->isComplete();            // Đã hoàn thành chưa
```

### Render UI
```php
echo $form->renderProgress();                 // Progress bar
echo $form->renderNavigation();               // Navigation buttons
echo $form->renderCSS();                      // CSS styles
```

## Tính năng

- ✅ **Cực kỳ đơn giản**: Chỉ cần 3-4 dòng code để khởi tạo
- ✅ **Auto session management**: Tự động lưu trữ dữ liệu
- ✅ **Bootstrap ready**: Sẵn CSS đẹp
- ✅ **Responsive**: Tự động responsive
- ✅ **No database required**: Chỉ dùng session
- ✅ **Flexible**: Có thể tùy chỉnh HTML theo ý muốn
- ✅ **Lightweight**: Code rất nhẹ và nhanh

## Form Actions

Form tự động xử lý các action:
- `action=next` - Đi đến step tiếp theo
- `action=prev` - Quay lại step trước
- `action=goto&target_step=3` - Đi đến step cụ thể

Chỉ cần đặt `name="action"` trong button submit.

Thế thôi! Đơn giản như vậy. 🚀

### 3. Step Content

Create individual step content files or use inline content:

```php
<!-- Step 1 Content -->
<div class="step-content" data-step="1">
    <div class="row">
        <div class="col-md-6">
            <label for="first_name" class="form-label">First Name *</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="col-md-6">
            <label for="last_name" class="form-label">Last Name *</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
        </div>
    </div>
</div>
```

## Configuration Options

```php
$options = [
    'totalSteps' => 10,              // Maximum number of steps
    'currentStep' => 1,              // Current active step
    'formId' => 'my-form',           // HTML form ID
    'submitUrl' => 'process.php',    // Form submission URL
    'holdSteps' => [3, 7, 10],       // Steps that require validation
    'enableAjax' => true,            // Enable AJAX submission
    'showProgress' => true,          // Show progress indicator
    'allowSkip' => false,            // Allow skipping non-required steps
    'saveProgress' => true,          // Save progress in session
    'theme' => 'default',            // UI theme (default, dark, custom)
    'stepUrl' => '?step=%d',         // URL pattern for step navigation
    'onStepChange' => 'handleStepChange', // JavaScript callback
    'onStepValidate' => 'validateStep',   // Validation callback
    'onComplete' => 'handleComplete'      // Completion callback
];
```

## Methods

### Core Methods

```php
// Set form steps
$formStep->setSteps(array $steps);

// Set current step
$formStep->setCurrentStep(int $step);

// Check if step is accessible
$formStep->canAccessStep(int $step);

// Mark step as completed
$formStep->completeStep(int $step);

// Check if step is completed
$formStep->isStepCompleted(int $step);

// Render the complete form
$formStep->render();

// Render just the progress indicator
$formStep->renderProgress();

// Render specific step content
$formStep->renderStep(int $step);

// Get step data
$formStep->getStep(int $step);

// Validate step
$formStep->validateStep(int $step, array $data);
```

### Navigation Methods

```php
// Go to next step
$formStep->nextStep();

// Go to previous step
$formStep->previousStep();

// Go to specific step
$formStep->goToStep(int $step);

// Get navigation URLs
$formStep->getStepUrl(int $step);
```

## JavaScript API

```javascript
// Initialize
const formStep = new FormStepManager({
    formId: 'my-form',
    totalSteps: 5,
    currentStep: 1,
    holdSteps: [3, 5],
    enableAjax: true
});

// Navigate to step
formStep.goToStep(3);

// Validate current step
formStep.validateCurrentStep();

// Submit step data
formStep.submitStep(stepNumber, formData);

// Event listeners
formStep.on('stepChange', function(step) {
    console.log('Changed to step:', step);
});

formStep.on('stepComplete', function(step) {
    console.log('Completed step:', step);
});

formStep.on('formComplete', function() {
    console.log('Form completed!');
});
```

## CSS Customization

Override default styles by modifying the CSS variables:

```css
:root {
    --form-step-primary-color: #0d6efd;
    --form-step-success-color: #198754;
    --form-step-warning-color: #ffc107;
    --form-step-danger-color: #dc3545;
    --form-step-border-radius: 0.375rem;
    --form-step-spacing: 1rem;
}
```

## Advanced Usage

### Custom Validation

```php
$formStep->setValidator(function($step, $data) {
    switch($step) {
        case 1:
            return !empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        case 3:
            return isset($data['terms']) && $data['terms'] === 'accepted';
        default:
            return true;
    }
});
```

### Custom Step Content

```php
$formStep->setStepContent(1, function($step) {
    return '<div class="custom-step-1">Custom HTML content</div>';
});
```

### AJAX Handlers

```php
// Handle AJAX requests
if ($_POST['action'] === 'submit_step') {
    $step = $_POST['step'];
    $data = $_POST['data'];
    
    if ($formStep->validateStep($step, $data)) {
        $formStep->saveStepData($step, $data);
        echo json_encode(['success' => true, 'nextStep' => $step + 1]);
    } else {
        echo json_encode(['success' => false, 'errors' => $formStep->getErrors()]);
    }
    exit;
}
```

## Examples

See the `examples/` directory for complete working examples:

- `examples/basic-form.php` - Simple 3-step form
- `examples/advanced-form.php` - Complex form with validation
- `examples/ajax-form.php` - AJAX-enabled form
- `examples/custom-styling.php` - Custom styled form

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Internet Explorer 11+

## License

MIT License - feel free to use in commercial and personal projects.

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## Changelog

### v1.0.0
- Initial release
- Support for 50 steps
- URL routing
- Hold step functionality
- AJAX support
- Bootstrap integration
