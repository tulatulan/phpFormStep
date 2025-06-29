<?php
/**
 * Ví dụ sử dụng phpFormStep cực kỳ đơn giản
 */

session_start();
require_once __DIR__ . '/../src/FormStepSimple.php';
use phpFormStep\FormStep;

// Khởi tạo form
$form = new FormStep('demo_form');

// Định nghĩa steps - chỉ cần array tên
$form->setSteps([
    'Thông tin cơ bản',
    'Chi tiết liên hệ', 
    'Tùy chọn',
    'Xem lại',
    'Hoàn thành'
]);

// Lấy thông tin hiện tại
$currentStep = $form->getCurrentStep();
$stepData = $form->getStepData();
$isComplete = $form->isComplete();

// Xử lý khi hoàn thành
if ($isComplete) {
    $allData = $form->getAllData();
    echo "<div class='alert alert-success'>Form đã hoàn thành! Dữ liệu: " . json_encode($allData) . "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Demo phpFormStep</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Demo phpFormStep - Đơn giản</h2>
        
        <!-- Progress bar tự động -->
        <?= $form->renderProgress() ?>
        
        <form method="POST">
            <div class="card">
                <div class="card-body">
                    
                    <?php if ($currentStep == 1): ?>
                        <h4>Thông tin cơ bản</h4>
                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?= $stepData['name'] ?? '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= $stepData['email'] ?? '' ?>" required>
                        </div>
                    
                    <?php elseif ($currentStep == 2): ?>
                        <h4>Chi tiết liên hệ</h4>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="<?= $stepData['phone'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea name="address" class="form-control" rows="3"><?= $stepData['address'] ?? '' ?></textarea>
                        </div>
                    
                    <?php elseif ($currentStep == 3): ?>
                        <h4>Tùy chọn</h4>
                        <div class="mb-3">
                            <label class="form-label">Nhận newsletter</label>
                            <select name="newsletter" class="form-control">
                                <option value="yes" <?= ($stepData['newsletter'] ?? '') == 'yes' ? 'selected' : '' ?>>Có</option>
                                <option value="no" <?= ($stepData['newsletter'] ?? '') == 'no' ? 'selected' : '' ?>>Không</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="terms" value="1" class="form-check-input" 
                                       <?= ($stepData['terms'] ?? '') ? 'checked' : '' ?>>
                                <label class="form-check-label">Đồng ý điều khoản</label>
                            </div>
                        </div>
                    
                    <?php elseif ($currentStep == 4): ?>
                        <h4>Xem lại thông tin</h4>
                        <?php $allData = $form->getAllData(); ?>
                        <table class="table">
                            <tr><td><strong>Họ tên:</strong></td><td><?= $allData[1]['name'] ?? 'Chưa nhập' ?></td></tr>
                            <tr><td><strong>Email:</strong></td><td><?= $allData[1]['email'] ?? 'Chưa nhập' ?></td></tr>
                            <tr><td><strong>Điện thoại:</strong></td><td><?= $allData[2]['phone'] ?? 'Chưa nhập' ?></td></tr>
                            <tr><td><strong>Địa chỉ:</strong></td><td><?= $allData[2]['address'] ?? 'Chưa nhập' ?></td></tr>
                            <tr><td><strong>Newsletter:</strong></td><td><?= $allData[3]['newsletter'] ?? 'Chưa chọn' ?></td></tr>
                        </table>
                    
                    <?php else: ?>
                        <h4>Cảm ơn!</h4>
                        <p>Thông tin của bạn đã được gửi thành công.</p>
                        <a href="?reset=1" class="btn btn-primary">Làm lại</a>
                    <?php endif; ?>
                    
                </div>
                
                <div class="card-footer">
                    <!-- Navigation buttons tự động -->
                    <?= $form->renderNavigation() ?>
                </div>
            </div>
        </form>
        
        <!-- CSS tự động -->
        <?= $form->renderCSS() ?>
        
        <!-- Debug info -->
        <div class="mt-4">
            <small class="text-muted">
                Current Step: <?= $currentStep ?> | 
                Data: <?= json_encode($stepData) ?> |
                <a href="?reset=1">Reset Form</a>
            </small>
        </div>
    </div>
</body>
</html>

<?php
// Reset form nếu cần
if (isset($_GET['reset'])) {
    unset($_SESSION['form_step_demo_form']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
