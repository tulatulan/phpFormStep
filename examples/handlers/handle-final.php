<?php
/**
 * Handler cuối cùng - Hoàn thành đăng ký
 */

// Kiểm tra có phải request từ form step không
if (!isset($_POST['final'])) {
    http_response_code(400);
    die('Invalid request');
}

// Lấy dữ liệu từ POST
$final_confirm = $_POST['final_confirm'] ?? '';
$marketing = $_POST['marketing'] ?? '';

// Validation
$errors = [];

if (empty($final_confirm)) {
    $errors[] = 'Bạn phải xác nhận thông tin';
}

if (!empty($errors)) {
    $_SESSION['formstep_errors'] = $errors;
    return false;
}

// Lấy tất cả dữ liệu từ các session
$allData = [];
for ($i = 1; $i <= 4; $i++) {
    $sessionKey = 'demo_v2_step_' . $i;
    if (isset($_SESSION[$sessionKey])) {
        $allData = array_merge($allData, $_SESSION[$sessionKey]);
    }
}

// Thêm thông tin cuối cùng
$allData['final_confirm'] = !empty($final_confirm);
$allData['marketing_consent'] = !empty($marketing);
$allData['completed_at'] = date('Y-m-d H:i:s');
$allData['user_temp_id'] = $_SESSION['user_temp_id'] ?? '';

// Giả lập lưu vào database cuối cùng
$logFile = __DIR__ . '/logs/final_registration.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logEntry = date('Y-m-d H:i:s') . ' - COMPLETED REGISTRATION - ' . json_encode($allData) . PHP_EOL;
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

// Gửi email thông báo (giả lập)
$emailData = [
    'to' => $allData['email'] ?? '',
    'subject' => 'Đăng ký thành công!',
    'body' => 'Chào ' . ($allData['name'] ?? 'bạn') . ', cảm ơn bạn đã hoàn thành đăng ký!',
    'sent_at' => date('Y-m-d H:i:s')
];

$emailLogFile = __DIR__ . '/logs/emails_sent.log';
$emailLogEntry = date('Y-m-d H:i:s') . ' - EMAIL SENT - ' . json_encode($emailData) . PHP_EOL;
file_put_contents($emailLogFile, $emailLogEntry, FILE_APPEND | LOCK_EX);

// Xóa session data (đã hoàn thành)
for ($i = 1; $i <= 4; $i++) {
    $sessionKey = 'demo_v2_step_' . $i;
    unset($_SESSION[$sessionKey]);
}
unset($_SESSION['demo_v2_current_step']);
unset($_SESSION['user_temp_id']);

return true;
