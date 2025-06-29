<?php
/**
 * Ví dụ sử dụng phpFormStep Professional
 * Cấu hình chuyên nghiệp cho multi-step form
 */

session_start();

// Include thư viện
require_once __DIR__ . '/../src/FormStepConfig.php';
require_once __DIR__ . '/../src/FormStepState.php';
require_once __DIR__ . '/../src/FormStepValidator.php';
require_once __DIR__ . '/../src/FormStepManager.php';

use phpFormStep\FormStepConfig;
use phpFormStep\FormStepManager;

// Cấu hình form
$config = new FormStepConfig([
    'totalSteps' => 4,              // Tổng số step
    'initStep' => 1,                // Step khởi tạo (tạo record)
    'requiredSaveSteps' => [1, 3],  // Step bắt buộc save trước khi next
    'allowNavigation' => true,      // Cho phép chuyển đổi giữa steps
    'mode' => 'create',             // Loại: 'create' hoặc 'edit'
    'sessionPrefix' => 'demo_form_',
    'tableName' => 'articles'       // Tên bảng (optional)
]);

// Mapping step với file PHP
$config->setStepFile(1, __DIR__ . '/demo-steps/step1-basic.php');
$config->setStepFile(2, __DIR__ . '/demo-steps/step2-details.php');
$config->setStepFile(3, __DIR__ . '/demo-steps/step3-content.php');
$config->setStepFile(4, __DIR__ . '/demo-steps/step4-review.php');

// Mapping step với handler functions
$config->setStepHandler(1, function($data, $state) {
    // Handler cho step 1 - tạo record cơ bản
    if (empty($data['title'])) {
        return ['success' => false, 'errors' => ['Title is required']];
    }
    
    // Giả lập tạo record trong database
    // $id = ArticleModel::create(['title' => $data['title']]);
    // $state->setPrimaryKeyValue($id);
    
    return ['success' => true, 'message' => 'Basic info saved'];
});

$config->setStepHandler(2, function($data, $state) {
    // Handler cho step 2 - cập nhật thông tin chi tiết
    // $primaryKey = $state->getPrimaryKeyValue();
    // ArticleModel::update($primaryKey, $data);
    
    return ['success' => true, 'message' => 'Details updated'];
});

$config->setStepHandler(3, function($data, $state) {
    // Handler cho step 3 - lưu content (bắt buộc)
    if (empty($data['content'])) {
        return ['success' => false, 'errors' => ['Content is required']];
    }
    
    // $primaryKey = $state->getPrimaryKeyValue();
    // ArticleModel::update($primaryKey, ['content' => $data['content']]);
    
    return ['success' => true, 'message' => 'Content saved'];
});

$config->setStepHandler(4, function($data, $state) {
    // Handler cho step 4 - hoàn thiện và publish
    // $primaryKey = $state->getPrimaryKeyValue();
    // ArticleModel::update($primaryKey, ['status' => 'published']);
    
    return ['success' => true, 'message' => 'Article published successfully!'];
});

// Validation rules
$config->setValidationRules(1, [
    'title' => 'required|min:5|max:100',
    'category' => 'required'
]);

$config->setValidationRules(3, [
    'content' => 'required|min:20'
]);

// Khởi tạo form manager
$formManager = new FormStepManager($config, 'demo_article');

// Lấy thông tin hiện tại
$currentStep = $formManager->getCurrentStep();
$stepData = $formManager->getStepData($currentStep);
$errors = $formManager->getErrors();
$isComplete = $formManager->isComplete();

// Xử lý hoàn thành
if ($isComplete) {
    $allData = $formManager->getAllStepData();
    $primaryKey = $formManager->getPrimaryKeyValue();
    
    echo "<div class='alert alert-success'>";
    echo "<h4>Form completed successfully!</h4>";
    echo "<p>Record ID: {$primaryKey}</p>";
    echo "<p>Total data collected: " . json_encode($allData) . "</p>";
    echo "</div>";
    
    echo "<a href='?reset=1' class='btn btn-primary'>Start New Form</a>";
    exit;
}

// Reset form nếu cần
if (isset($_GET['reset'])) {
    $formManager->reset();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>phpFormStep Professional Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <h2 class="mb-4">phpFormStep Professional Demo</h2>
                
                <!-- Progress Bar -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="progress mb-3">
                            <?php $progress = ($currentStep / $config->totalSteps) * 100; ?>
                            <div class="progress-bar" style="width: <?= $progress ?>%"></div>
                        </div>
                        
                        <div class="row text-center">
                            <?php for ($i = 1; $i <= $config->totalSteps; $i++): ?>
                                <?php 
                                $isActive = ($i === $currentStep);
                                $isCompleted = $formManager->isStepCompleted($i);
                                $isRequired = $config->isRequiredSaveStep($i);
                                ?>
                                <div class="col">
                                    <div class="step-indicator <?= $isActive ? 'active' : '' ?> <?= $isCompleted ? 'completed' : '' ?>">
                                        <div class="step-number">
                                            <?php if ($isCompleted): ?>
                                                <i class="fas fa-check"></i>
                                            <?php else: ?>
                                                <?= $i ?>
                                            <?php endif; ?>
                                            <?php if ($isRequired): ?>
                                                <small class="text-danger">*</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="step-title">Step <?= $i ?></div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Error Messages -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h6>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Form -->
                <form method="POST">
                    <div class="card">
                        <div class="card-body">
                            <!-- Render current step -->
                            <?= $formManager->renderStep($currentStep) ?>
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <?php if ($currentStep > 1): ?>
                                        <button type="submit" name="action" value="prev" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left"></i> Previous
                                        </button>
                                    <?php endif; ?>
                                </div>
                                
                                <div>
                                    <button type="submit" name="action" value="save" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                    
                                    <?php if ($currentStep < $config->totalSteps): ?>
                                        <button type="submit" name="action" value="next" class="btn btn-primary">
                                            Next <i class="fas fa-arrow-right"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="complete" class="btn btn-success">
                                            <i class="fas fa-check"></i> Complete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Debug Info -->
                <div class="mt-4">
                    <small class="text-muted">
                        Current Step: <?= $currentStep ?> | 
                        Primary Key: <?= $formManager->getPrimaryKeyValue() ?? 'Not set' ?> |
                        Mode: <?= $config->mode ?> |
                        <a href="?reset=1">Reset Form</a>
                    </small>
                </div>
                
            </div>
        </div>
    </div>
    
    <style>
    .step-indicator {
        text-align: center;
        padding: 10px 5px;
    }
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: bold;
        position: relative;
    }
    .step-indicator.active .step-number {
        background: #0d6efd;
        color: white;
    }
    .step-indicator.completed .step-number {
        background: #198754;
        color: white;
    }
    .step-title {
        font-size: 12px;
        color: #6c757d;
    }
    .step-indicator.active .step-title {
        color: #0d6efd;
        font-weight: bold;
    }
    .step-indicator.completed .step-title {
        color: #198754;
    }
    </style>
</body>
</html>
