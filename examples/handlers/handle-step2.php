<?php
/**
 * Handler cho Step 2 - Xử lý thông tin liên hệ
 */

// Kiểm tra có phải request từ form step không
if (!isset($_POST['step2'])) {
    http_response_code(400);
    die('Invalid request');
}

// Lấy dữ liệu từ POST
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$emergency_contact = $_POST['emergency_contact'] ?? '';

// Validation
$errors = [];

if (!empty($phone)) {
    if (!preg_match('/^[\d\s\-\+\(\)]+$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ';
    }
}

if (!empty($errors)) {
    $_SESSION['formstep_errors'] = $errors;
    return false;
}

// Lưu dữ liệu
$contactData = [
    'user_temp_id' => $_SESSION['user_temp_id'] ?? '',
    'phone' => $phone,
    'address' => $address,
    'city' => $city,
    'emergency_contact' => $emergency_contact,
    'updated_at' => date('Y-m-d H:i:s')
];

// Giả lập lưu vào file
$logFile = __DIR__ . '/logs/step2_data.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($contactData) . PHP_EOL;
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

return true;
