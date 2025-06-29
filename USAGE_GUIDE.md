# phpFormStep Library - Packaged & Protected

## 📦 Thư viện đã được đóng gói chuyên nghiệp

Thư viện phpFormStep đã được đóng gói và bảo vệ với cấu trúc chuyên nghiệp, không nên chỉnh sửa trực tiếp.

## 🛡️ Bảo vệ thư viện

### Các tính năng bảo vệ đã triển khai:

✅ **Bootstrap System** - Entry point duy nhất qua `bootstrap.php`
✅ **Autoloader** - Tự động load classes mà không cần include thủ công
✅ **Access Protection** - Ngăn chặn truy cập trực tiếp vào file thư viện
✅ **Version Management** - Quản lý phiên bản chuyên nghiệp
✅ **Composer Support** - Hỗ trợ cả Composer và manual loading

## 🚀 Cách sử dụng đúng

### 1. Include thư viện (CÁCH DUY NHẤT)

```php
<?php
// ✅ ĐÚNG - Sử dụng bootstrap
require_once 'lib/phpFormStep/bootstrap.php';

use phpFormStep\FormStepConfig;
use phpFormStep\FormStepManager;

// ❌ SAI - Không include trực tiếp các file trong src/
// require_once 'lib/phpFormStep/src/FormStepConfig.php';
```

### 2. Cấu hình và sử dụng

```php
// Tạo cấu hình
$config = new FormStepConfig([
    'totalSteps' => 3,
    'initStep' => 1,
    'requiredSaveSteps' => [1, 3],
    'allowNavigation' => true,
    'mode' => 'create',
    'sessionPrefix' => 'my_form_',
]);

// Cấu hình steps
$config->setStepFile(1, 'views/step1.php');
$config->setStepFile(2, 'views/step2.php');
$config->setStepFile(3, 'views/step3.php');

// Khởi tạo manager
$formManager = new FormStepManager($config, 'form_id');
```

## 📁 Cấu trúc thư viện được bảo vệ

```
lib/phpFormStep/
├── 📄 bootstrap.php          ← ENTRY POINT DUY NHẤT
├── 📄 autoload.php          ← Auto-loader
├── 📄 composer.json         ← Composer config
├── 📄 LICENSE               ← MIT License
├── 📄 VERSION               ← Version info
├── 📄 CHANGELOG.md          ← Change history
├── 📄 README.md             ← Documentation
├── 🔒 src/                  ← PROTECTED CORE
│   ├── 🔒 FormStepConfig.php
│   ├── 🔒 FormStepState.php
│   ├── 🔒 FormStepValidator.php
│   ├── 🔒 FormStepManager.php
│   └── 🔒 _protection.php
├── 📁 examples/             ← Usage examples
├── 📁 test/                 ← Test files
└── 📁 docs/                 ← Documentation
```

## 🔐 Quy tắc bảo vệ

### ✅ ĐƯỢC PHÉP:
- Sử dụng thư viện qua `bootstrap.php`
- Tạo cấu hình và sử dụng API công khai
- Tạo step files và handlers riêng
- Extend classes nếu cần thiết
- Đọc documentation và examples

### ❌ KHÔNG ĐƯỢC PHÉP:
- Chỉnh sửa trực tiếp files trong `src/`
- Include trực tiếp files trong `src/`
- Bypass bootstrap system
- Sửa đổi core logic của thư viện
- Xóa protection code

## 🔧 API Công khai

### FormStepConfig
```php
$config = new FormStepConfig([...]);
$config->setStepFile($step, $filePath);
$config->setStepHandler($step, $callable);
$config->setValidationRules($step, $rules);
```

### FormStepManager
```php
$manager = new FormStepManager($config, $formId);
$manager->getCurrentStep();
$manager->getStepData($step);
$manager->renderStep($step);
$manager->getErrors();
$manager->isComplete();
```

## 🧪 Testing

```bash
# Test thư viện
cd lib/phpFormStep/test
php quick-test.php
```

## 📖 Documentation

- `README.md` - Hướng dẫn đầy đủ
- `CHANGELOG.md` - Lịch sử thay đổi
- `examples/` - Ví dụ sử dụng
- `INTEGRATION_SUCCESS.md` - Báo cáo tích hợp

## 🎯 Lợi ích của việc đóng gói

1. **Bảo mật**: Ngăn chặn chỉnh sửa không mong muốn
2. **Ổn định**: Đảm bảo thư viện hoạt động nhất quán
3. **Chuyên nghiệp**: Cấu trúc chuẩn công nghiệp
4. **Dễ bảo trì**: Tách biệt core và application code
5. **Tương thích**: Hỗ trợ nhiều cách integration

## ⚠️ Lưu ý quan trọng

- **LUÔN sử dụng `bootstrap.php`** làm entry point
- **KHÔNG chỉnh sửa** files trong thư mục `src/`
- **Tạo extension** nếu cần tính năng mới
- **Báo cáo bug** thay vì tự sửa core
- **Đọc documentation** trước khi sử dụng

Thư viện phpFormStep giờ đây đã được đóng gói và bảo vệ một cách chuyên nghiệp! 🎉
