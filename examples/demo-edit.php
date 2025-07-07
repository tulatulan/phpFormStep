<?php
/**
 * Demo Edit Mode - phpFormStep v2.0
 * 
 * Ví dụ sử dụng Edit Mode để chỉnh sửa thông tin
 */

// Include thư viện
require_once __DIR__ . '/../dist/phpFormStep.php';

// Giả lập dữ liệu có sẵn (trong thực tế sẽ load từ database)
$existingData = [
    'step1' => [
        'name' => 'Nguyễn Văn A',
        'email' => 'nguyenvana@email.com',
        'birth_date' => '1990-01-15',
        'gender' => 'male'
    ],
    'step2' => [
        'address' => '123 Đường ABC, Quận 1',
        'city' => 'Ho Chi Minh City',
        'phone' => '0123456789',
        'company' => 'Công ty TNHH ABC'
    ],
    'step3' => [
        'interests' => ['technology', 'reading'],
        'newsletter' => 'yes',
        'bio' => 'Tôi là một lập trình viên với 5 năm kinh nghiệm.',
        'terms' => 'on'
    ]
];

// Load dữ liệu vào session để edit
session_start();
foreach ($existingData as $stepKey => $stepData) {
    $_SESSION['demo_edit_' . $stepKey] = $stepData;
}

// Cấu hình form step cho Edit Mode
$config = [
    'mode' => 'edit',                      // Edit mode
    'totalSteps' => 4,                     // Tổng số step
    'initStep' => 1,                       // Step khởi tạo (create mode)
    'editStep' => 2,                       // Step edit - bắt đầu từ step 2
    'sessionPrefix' => 'demo_edit_',       // Prefix cho session
    'debug' => true,                       // Hiển thị debug info
    
    // Cấu hình từng step
    'steps' => [
        1 => [
            'title' => 'Thông tin cơ bản',
            'buttonNext' => [
                'label' => 'Cập nhật và tiếp tục',
                'submitBeforeContinue' => true,
                'required' => true,
                'validate' => function($postData, $stepData) {
                    $errors = [];
                    if (empty($postData['name'])) {
                        $errors[] = 'Tên không được để trống';
                    }
                    if (empty($postData['email'])) {
                        $errors[] = 'Email không được để trống';
                    } elseif (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = 'Email không hợp lệ';
                    }
                    return empty($errors) ? true : $errors;
                }
            ],
            'buttonPrev' => [
                'label' => 'Quay lại',
                'clearInput' => false
            ],
            'loadView' => [
                'isFunctionView' => false,
                'loadView' => __DIR__ . '/views/step1.php'
            ],
            'handleSubmit' => [
                'URLhandle' => __DIR__ . '/handlers/handle-step1.php',
                'successRedirectURL' => '',
                'falseRedirectURL' => '',
                'inputKeyForForm' => 'step1'
            ]
        ],
        
        2 => [
            'title' => 'Thông tin liên hệ',
            'buttonNext' => [
                'label' => 'Cập nhật và tiếp tục',
                'submitBeforeContinue' => true,
                'required' => true,
                'validate' => function($postData, $stepData) {
                    $errors = [];
                    if (empty($postData['address'])) {
                        $errors[] = 'Địa chỉ không được để trống';
                    }
                    if (empty($postData['city'])) {
                        $errors[] = 'Thành phố không được để trống';
                    }
                    if (empty($postData['phone'])) {
                        $errors[] = 'Số điện thoại không được để trống';
                    }
                    return empty($errors) ? true : $errors;
                }
            ],
            'buttonPrev' => [
                'label' => 'Quay lại',
                'clearInput' => false
            ],
            'loadView' => [
                'isFunctionView' => false,
                'loadView' => __DIR__ . '/views/step2.php'
            ],
            'handleSubmit' => [
                'URLhandle' => __DIR__ . '/handlers/handle-step2.php',
                'successRedirectURL' => '',
                'falseRedirectURL' => '',
                'inputKeyForForm' => 'step2'
            ]
        ],
        
        3 => [
            'title' => 'Sở thích và tùy chọn',
            'buttonNext' => [
                'label' => 'Cập nhật và tiếp tục',
                'submitBeforeContinue' => true,
                'required' => true,
                'validate' => function($postData, $stepData) {
                    $errors = [];
                    if (empty($postData['terms'])) {
                        $errors[] = 'Bạn phải đồng ý với điều khoản sử dụng';
                    }
                    return empty($errors) ? true : $errors;
                }
            ],
            'buttonPrev' => [
                'label' => 'Quay lại',
                'clearInput' => false
            ],
            'loadView' => [
                'isFunctionView' => false,
                'loadView' => __DIR__ . '/views/step3.php'
            ],
            'handleSubmit' => [
                'URLhandle' => __DIR__ . '/handlers/handle-step3.php',
                'successRedirectURL' => '',
                'falseRedirectURL' => '',
                'inputKeyForForm' => 'step3'
            ]
        ],
        
        4 => [
            'title' => 'Xem lại và xác nhận',
            'buttonNext' => [
                'label' => 'Cập nhật thông tin',
                'submitBeforeContinue' => false,
                'required' => false
            ],
            'buttonPrev' => [
                'label' => 'Quay lại',
                'clearInput' => false
            ],
            'loadView' => [
                'isFunctionView' => false,
                'loadView' => __DIR__ . '/views/step4.php'
            ],
            'handleSubmit' => [
                'URLhandle' => __DIR__ . '/handlers/handle-final.php',
                'successRedirectURL' => '?success=1',
                'falseRedirectURL' => '?error=1',
                'inputKeyForForm' => 'step4'
            ]
        ]
    ]
];

// Khởi tạo form step
$formStep = new phpFormStep($config);

// Xử lý success/error
$message = '';
$messageType = '';

if (isset($_GET['success'])) {
    $message = '✅ Cập nhật thông tin thành công!';
    $messageType = 'success';
} elseif (isset($_GET['error'])) {
    $message = '❌ Có lỗi xảy ra khi cập nhật thông tin!';
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mode Demo - phpFormStep v2.0</title>
    
    <!-- Core CSS (Required) -->
    <link rel="stylesheet" href="../dist/formstep.css">
    <!-- Example CSS (Optional styling) -->
    <link rel="stylesheet" href="assets/css/formstep.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2d3748;
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .header p {
            color: #718096;
            font-size: 1.1em;
            margin-bottom: 20px;
        }
        
        .mode-badge {
            background: #667eea;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }
        
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }
        
        .message.error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8181;
        }
        
        .links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            margin: 0 15px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Edit Mode Demo</h1>
            <p>Chỉnh sửa thông tin người dùng với phpFormStep v2.0</p>
            <span class="mode-badge">Edit Mode</span>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <!-- Render form -->
        <?= $formStep->render() ?>
        
        <div class="links">
            <a href="demo-v2.php">🆕 Create Mode Demo</a>
            <a href="debug.php">🔍 Debug Info</a>
            <a href="check-logs.php">📝 Check Logs</a>
        </div>
    </div>
    
    <!-- Core JavaScript (Required) -->
    <script>
        var FormStepConfig = <?= $formStep->getJavaScriptConfig() ?>;
    </script>
    <script src="../dist/formstep.js"></script>
    <!-- Example JavaScript (Optional enhancements) -->
    <script src="assets/js/formstep.js"></script>
</body>
</html>
