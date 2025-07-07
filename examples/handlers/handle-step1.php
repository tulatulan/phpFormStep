<?php
/**
 * Handler cho Step 1 - Xử lý thông tin cơ bản
 */

// Kiểm tra có phải request từ form step không
if (!isset($_POST['step1'])) {
    http_response_code(400);
    die('Invalid request');
}

// Lấy dữ liệu từ POST
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$birth_date = $_POST['birth_date'] ?? '';
$gender = $_POST['gender'] ?? '';

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Tên không được để trống';
}

if (empty($email)) {
    $errors[] = 'Email không được để trống';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email không hợp lệ';
}

// Kiểm tra email đã tồn tại (giả lập)
$existingEmails = ['admin@example.com', 'test@example.com'];
if (in_array($email, $existingEmails)) {
    $errors[] = 'Email này đã được sử dụng';
}

if (!empty($errors)) {
    // Trả về lỗi (có thể redirect hoặc set session)
    $_SESSION['formstep_errors'] = $errors;
    return false;
}

// Lưu dữ liệu vào database (giả lập)
$userData = [
    'name' => $name,
    'email' => $email,
    'birth_date' => $birth_date,
    'gender' => $gender,
    'created_at' => date('Y-m-d H:i:s')
];

// Giả lập lưu vào file (trong thực tế sẽ lưu vào database)
$logFile = __DIR__ . '/logs/step1_data.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($userData) . PHP_EOL;
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

// Lưu ID hoặc key để sử dụng cho các step tiếp theo
$_SESSION['user_temp_id'] = uniqid('user_', true);

// Thành công
return true;
