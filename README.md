# phpFormStep v2.0 - Professional Multi-Step Form Library

## 🚀 Giới thiệu

phpFormStep v2.0 là thư viện PHP chuyên nghiệp để tạo form nhiều bước với các tính năng:

- ✅ **Thuần JavaScript + CSS** - Không phụ thuộc jQuery hay Bootstrap
- ✅ **Dễ sử dụng** - Chỉ cần include 1 file chính
- ✅ **Linh hoạt** - Hỗ trợ Create Mode và Edit Mode
- ✅ **Validation mạnh mẽ** - Validation tùy chỉnh cho từng step
- ✅ **Handler riêng biệt** - Xử lý dữ liệu độc lập cho từng step
- ✅ **Responsive** - Giao diện đẹp trên mọi thiết bị
- ✅ **Professional** - Có thể tái sử dụng ở bất kỳ dự án nào

## 📦 Cài đặt

```bash
# Copy thư mục dist vào project của bạn
cp -r dist/ your-project/phpFormStep/
```

## 🔧 Cách sử dụng cơ bản

### 1. Include thư viện

```php
<?php
require_once 'phpFormStep/phpFormStep.php';
```

### 2. Cấu hình form

```php
$config = [
    'mode' => 'create',                    // 'create' hoặc 'edit'
    'totalSteps' => 3,                     // Tổng số step
    'initStep' => 1,                       // Step khởi tạo (create mode)
    'editStep' => 2,                       // Step edit (edit mode)
    'sessionPrefix' => 'my_form_',         // Prefix cho session
    'debug' => false,                      // Hiển thị debug info
    
    'steps' => [
        1 => [
            'title' => 'Thông tin cơ bản',
            'buttonNext' => [
                'label' => 'Tiếp tục',
                'submitBeforeContinue' => true,
                'required' => true,
                'validate' => function($postData, $stepData) {
                    if (empty($postData['name'])) {
                        return 'Tên không được để trống';
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
                'loadView' => './views/step1.php'
            ],
            'handleSubmit' => [
                'URLhandle' => './handlers/handle-step1.php',
                'inputKeyForForm' => 'step1'
            ]
        ],
        // ... các step khác
    ]
];
```

### 3. Khởi tạo và render

```php
$formStep = new phpFormStep($config);
echo $formStep->render();
```

## 📋 Cấu trúc Configuration

### Cấu hình chính

| Thuộc tính | Loại | Mô tả |
|------------|------|--------|
| `mode` | string | `'create'` hoặc `'edit'` |
| `totalSteps` | int | Tổng số bước |
| `initStep` | int | Bước khởi tạo (create mode) |
| `editStep` | int | Bước edit (edit mode) |
| `sessionPrefix` | string | Prefix cho session |
| `debug` | bool | Hiển thị debug info |

### Cấu hình mỗi step

```php
[
    'title' => 'Tiêu đề step',
    'buttonNext' => [
        'label' => 'Nhãn nút Next',
        'submitBeforeContinue' => true/false,
        'required' => true/false,
        'validate' => function($postData, $stepData) {
            // Logic validation
            return true; // hoặc string lỗi
        }
    ],
    'buttonPrev' => [
        'label' => 'Nhãn nút Previous',
        'clearInput' => true/false
    ],
    'loadView' => [
        'isFunctionView' => false,
        'loadView' => 'path/to/view.php'
    ],
    'handleSubmit' => [
        'URLhandle' => 'path/to/handler.php',
        'successRedirectURL' => 'success.php',
        'falseRedirectURL' => 'error.php',
        'inputKeyForForm' => 'step1'
    ]
]
```

## 🎯 Tính năng chính

### 1. **Validation mạnh mẽ**
- Validation tùy chỉnh cho từng step
- Validation realtime với JavaScript
- Hiển thị lỗi friendly

### 2. **Handler riêng biệt**
- Mỗi step có handler riêng
- Xử lý dữ liệu độc lập
- Hỗ trợ redirect sau khi xử lý

### 3. **Navigation linh hoạt**
- Next/Previous với animation
- Goto step tùy ý
- Keyboard shortcuts (Ctrl+Arrow)

### 4. **Data Management**
- Lưu dữ liệu vào session
- Auto-save khi nhập liệu
- Backup localStorage

### 5. **UI/UX chuyên nghiệp**
- Progress bar với animation
- Loading states
- Responsive design
- Toast notifications

## 🔧 API Methods

### Khởi tạo
```php
$formStep = new phpFormStep($config);
```

### Render form
```php
echo $formStep->render();
```

### Lấy thông tin
```php
$currentStep = $formStep->getCurrentStep();
$allData = $formStep->getAllStepData();
$errors = $formStep->getErrors();
$isCompleted = $formStep->isCompleted();
```

### Reset form
```php
$formStep->reset();
```

## 🎨 Tùy chỉnh CSS

Thư viện sử dụng CSS classes có prefix `formstep-` để dễ dàng tùy chỉnh:

```css
.formstep-progress { /* Progress bar */ }
.formstep-content { /* Nội dung step */ }
.formstep-navigation { /* Navigation buttons */ }
.formstep-btn { /* Buttons */ }
.formstep-form-group { /* Form groups */ }
.formstep-input { /* Input fields */ }
```

## 🔒 Bảo mật

- Validation cả client và server
- CSRF protection
- Session security
- Input sanitization
- Error handling

## 📱 Responsive Design

- Mobile-first approach
- Touch-friendly interface
- Adaptive layouts
- Progressive enhancement

## 🐛 Debug Mode

Bật debug mode để xem thông tin chi tiết:

```php
$config['debug'] = true;
```

## 📞 Hỗ trợ

- **Email**: support@example.com
- **Documentation**: [Link docs]
- **Issues**: [Github Issues]

## 📄 License

MIT License - Sử dụng tự do cho mọi dự án.

---

**phpFormStep v2.0** - Professional Multi-Step Form Library for PHP 🚀
