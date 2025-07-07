<?php
/**
 * Handler cho Step 3 - Xử lý sở thích và tùy chọn
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra có phải request từ form step không
if (!isset($_POST['step3'])) {
    http_response_code(400);
    die('Invalid request');
}

// Lấy dữ liệu từ POST
$interests = $_POST['interests'] ?? [];
$newsletter = $_POST['newsletter'] ?? '';
$bio = $_POST['bio'] ?? '';
$terms = $_POST['terms'] ?? '';

// Validation
$errors = [];

if (empty($terms)) {
    $errors[] = 'Bạn phải đồng ý với điều khoản sử dụng';
}

if (!empty($errors)) {
    $_SESSION['formstep_errors'] = $errors;
    return false;
}

// Lưu dữ liệu
$preferencesData = [
    'user_temp_id' => $_SESSION['user_temp_id'] ?? '',
    'interests' => $interests,
    'newsletter' => $newsletter,
    'bio' => $bio,
    'terms_accepted' => !empty($terms),
    'updated_at' => date('Y-m-d H:i:s')
];

// Giả lập lưu vào file
$logFile = __DIR__ . '/logs/step3_data.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($preferencesData) . PHP_EOL;
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

return true;
