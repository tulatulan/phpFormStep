<?php
/**
 * Demo sử dụng thư viện phpFormStep v2.0
 * 
 * Ví dụ về cách sử dụng thư viện mới với cấu hình chuyên nghiệp
 */

// Include thư viện
require_once __DIR__ . '/../dist/phpFormStep.php';

// Cấu hình form step
$config = [
    'mode' => 'create',                    // 'create' hoặc 'edit'
    'totalSteps' => 4,                     // Tổng số step
    'initStep' => 1,                       // Step khởi tạo (create mode)
    'editStep' => 2,                       // Step edit (edit mode)
    'sessionPrefix' => 'demo_v2_',         // Prefix cho session
    'debug' => true,                       // Hiển thị debug info
    
    // Cấu hình từng step
    'steps' => [
        1 => [
            'title' => 'Thông tin cơ bản',
            'buttonNext' => [
                'label' => 'Lưu và tiếp tục',
                'submitBeforeContinue' => true,
                'required' => true,
                'validate' => function($postData, $stepData) {
                    // Validation logic
                    if (empty($postData['name'])) {
                        return 'Tên không được để trống';
                    }
                    if (empty($postData['email'])) {
                        return 'Email không được để trống';
                    }
                    if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
                        return 'Email không hợp lệ';
                    }
                    return true;
                }
            ],
            'buttonPrev' => [
                'label' => 'Trở lại',
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
                'label' => 'Tiếp tục',
                'submitBeforeContinue' => false,
                'required' => true,
                'validate' => function($postData, $stepData) {
                    if (!empty($postData['phone'])) {
                        if (!preg_match('/^[\d\s\-\+\(\)]+$/', $postData['phone'])) {
                            return 'Số điện thoại không hợp lệ';
                        }
                    }
                    return true;
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
                'inputKeyForForm' => 'step2'
            ]
        ],
        
        3 => [
            'title' => 'Tùy chọn và sở thích',
            'buttonNext' => [
                'label' => 'Xem lại thông tin',
                'submitBeforeContinue' => false,
                'required' => true,
                'validate' => function($postData, $stepData) {
                    if (empty($postData['terms'])) {
                        return 'Bạn phải đồng ý với điều khoản sử dụng';
                    }
                    return true;
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
                'inputKeyForForm' => 'step3'
            ]
        ],
        
        4 => [
            'title' => 'Xem lại và hoàn thành',
            'buttonNext' => [
                'label' => 'Hoàn thành đăng ký',
                'submitBeforeContinue' => true,
                'required' => true,
                'validate' => function($postData, $stepData) {
                    if (empty($postData['final_confirm'])) {
                        return 'Vui lòng xác nhận thông tin';
                    }
                    return true;
                }
            ],
            'buttonPrev' => [
                'label' => 'Quay lại chỉnh sửa',
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
                'inputKeyForForm' => 'final'
            ]
        ]
    ]
];

// Khởi tạo form step
try {
    $formStep = new phpFormStep($config);
    
    // Xử lý kết quả
    if (isset($_GET['success'])) {
        echo '<div style="padding: 20px; background: #d4edda; color: #155724; border-radius: 8px; margin: 20px;">';
        echo '<h2>🎉 Đăng ký thành công!</h2>';
        echo '<p>Cảm ơn bạn đã hoàn thành quá trình đăng ký.</p>';
        echo '<a href="?" style="color: #007bff;">Đăng ký mới</a>';
        echo '</div>';
        exit;
    }
    
    if (isset($_GET['error'])) {
        echo '<div style="padding: 20px; background: #f8d7da; color: #721c24; border-radius: 8px; margin: 20px;">';
        echo '<h2>❌ Có lỗi xảy ra!</h2>';
        echo '<p>Vui lòng thử lại sau.</p>';
        echo '<a href="?" style="color: #007bff;">Thử lại</a>';
        echo '</div>';
        exit;
    }
    
    if (isset($_GET['reset'])) {
        $formStep->reset();
        header('Location: ?');
        exit;
    }
    
} catch (Exception $e) {
    echo '<div style="padding: 20px; background: #f8d7da; color: #721c24; border-radius: 8px; margin: 20px;">';
    echo '<h2>Configuration Error</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>phpFormStep v2.0 - Professional Demo</title>
    <link rel="stylesheet" href="assets/css/formstep.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        
        .form-container {
            padding: 0;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        
        .footer a {
            color: #007bff;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>phpFormStep v2.0</h1>
            <p>Professional Multi-Step Form Library</p>
        </div>
        
        <div class="form-container">
            <form method="POST" id="mainForm">
                <?= $formStep->render() ?>
            </form>
        </div>
        
        <div class="footer">
            <a href="?reset=1">Reset Form</a>
            <a href="?mode=edit">Edit Mode</a>
            <a href="?">Create Mode</a>
        </div>
    </div>
    
    <script>
        var FormStepConfig = <?= $formStep->getJavaScriptConfig() ?>;
    </script>
    <script src="assets/js/formstep.js"></script>
</body>
</html>
