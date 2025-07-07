<?php
// Lấy tất cả dữ liệu từ các step trước
$allData = [];
for ($i = 1; $i <= 3; $i++) {
    $sessionKey = 'demo_v2_step_' . $i;
    if (isset($_SESSION[$sessionKey])) {
        $allData[$i] = $_SESSION[$sessionKey];
    }
}

$step1Data = $allData[1] ?? [];
$step2Data = $allData[2] ?? [];
$step3Data = $allData[3] ?? [];
?>

<div class="formstep-success">
    <h4>🎉 Xem lại thông tin trước khi hoàn thành</h4>
    <p>Vui lòng kiểm tra lại tất cả thông tin bên dưới trước khi hoàn thành đăng ký.</p>
</div>

<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h5 style="color: #495057; margin-bottom: 15px;">📋 Thông tin cá nhân</h5>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
        <div>
            <strong>Họ tên:</strong><br>
            <span><?= htmlspecialchars($step1Data['name'] ?? 'Chưa nhập') ?></span>
        </div>
        <div>
            <strong>Email:</strong><br>
            <span><?= htmlspecialchars($step1Data['email'] ?? 'Chưa nhập') ?></span>
        </div>
        <div>
            <strong>Ngày sinh:</strong><br>
            <span><?= htmlspecialchars($step1Data['birth_date'] ?? 'Chưa nhập') ?></span>
        </div>
        <div>
            <strong>Giới tính:</strong><br>
            <span><?= htmlspecialchars($step1Data['gender'] ?? 'Chưa chọn') ?></span>
        </div>
    </div>
</div>

<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h5 style="color: #495057; margin-bottom: 15px;">📞 Thông tin liên hệ</h5>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
        <div>
            <strong>Điện thoại:</strong><br>
            <span><?= htmlspecialchars($step2Data['phone'] ?? 'Chưa nhập') ?></span>
        </div>
        <div>
            <strong>Thành phố:</strong><br>
            <span><?= htmlspecialchars($step2Data['city'] ?? 'Chưa chọn') ?></span>
        </div>
        <div style="grid-column: 1 / -1;">
            <strong>Địa chỉ:</strong><br>
            <span><?= htmlspecialchars($step2Data['address'] ?? 'Chưa nhập') ?></span>
        </div>
        <div style="grid-column: 1 / -1;">
            <strong>Liên hệ khẩn cấp:</strong><br>
            <span><?= htmlspecialchars($step2Data['emergency_contact'] ?? 'Chưa nhập') ?></span>
        </div>
    </div>
</div>

<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h5 style="color: #495057; margin-bottom: 15px;">🎯 Sở thích và tùy chọn</h5>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
        <div>
            <strong>Sở thích:</strong><br>
            <span><?= !empty($step3Data['interests']) ? implode(', ', $step3Data['interests']) : 'Chưa chọn' ?></span>
        </div>
        <div>
            <strong>Nhận thông báo:</strong><br>
            <span><?= htmlspecialchars($step3Data['newsletter'] ?? 'Chưa chọn') ?></span>
        </div>
        <div style="grid-column: 1 / -1;">
            <strong>Giới thiệu:</strong><br>
            <span><?= htmlspecialchars($step3Data['bio'] ?? 'Chưa nhập') ?></span>
        </div>
    </div>
</div>

<div class="formstep-form-group">
    <label class="formstep-checkbox-label">
        <input type="checkbox" name="final_confirm" value="1" class="formstep-checkbox" required
               <?= ($stepData['final_confirm'] ?? '') ? 'checked' : '' ?>>
        Tôi xác nhận rằng tất cả thông tin trên là chính xác và đầy đủ <span style="color: red;">*</span>
    </label>
</div>

<div class="formstep-form-group">
    <label class="formstep-checkbox-label">
        <input type="checkbox" name="marketing" value="1" class="formstep-checkbox"
               <?= ($stepData['marketing'] ?? '') ? 'checked' : '' ?>>
        Tôi đồng ý nhận thông tin khuyến mãi và cập nhật từ hệ thống
    </label>
</div>

<div class="formstep-warning">
    <strong>⚠️ Lưu ý quan trọng:</strong><br>
    Sau khi nhấn "Hoàn thành đăng ký", bạn sẽ không thể chỉnh sửa thông tin này. 
    Vui lòng kiểm tra kỹ trước khi tiếp tục.
</div>
