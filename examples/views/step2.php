<div class="formstep-form-group">
    <label class="formstep-label" for="phone">Số điện thoại</label>
    <input type="tel" id="phone" name="phone" class="formstep-input" 
           value="<?= htmlspecialchars($stepData['phone'] ?? '') ?>" 
           placeholder="0123456789">
</div>

<div class="formstep-form-group">
    <label class="formstep-label" for="address">Địa chỉ</label>
    <textarea id="address" name="address" class="formstep-textarea" 
              placeholder="Nhập địa chỉ đầy đủ" rows="4"><?= htmlspecialchars($stepData['address'] ?? '') ?></textarea>
</div>

<div class="formstep-form-group">
    <label class="formstep-label" for="city">Thành phố</label>
    <select id="city" name="city" class="formstep-select">
        <option value="">Chọn thành phố</option>
        <option value="hanoi" <?= ($stepData['city'] ?? '') === 'hanoi' ? 'selected' : '' ?>>Hà Nội</option>
        <option value="hcm" <?= ($stepData['city'] ?? '') === 'hcm' ? 'selected' : '' ?>>TP. Hồ Chí Minh</option>
        <option value="danang" <?= ($stepData['city'] ?? '') === 'danang' ? 'selected' : '' ?>>Đà Nẵng</option>
        <option value="haiphong" <?= ($stepData['city'] ?? '') === 'haiphong' ? 'selected' : '' ?>>Hải Phòng</option>
        <option value="other" <?= ($stepData['city'] ?? '') === 'other' ? 'selected' : '' ?>>Khác</option>
    </select>
</div>

<div class="formstep-form-group">
    <label class="formstep-label" for="emergency_contact">Liên hệ khẩn cấp</label>
    <input type="text" id="emergency_contact" name="emergency_contact" class="formstep-input" 
           value="<?= htmlspecialchars($stepData['emergency_contact'] ?? '') ?>" 
           placeholder="Tên và số điện thoại người thân">
</div>

<p style="color: #6c757d; font-size: 14px; margin-top: 20px;">
    <strong>Thông tin liên hệ:</strong> Những thông tin này sẽ giúp chúng tôi liên lạc với bạn khi cần thiết.
</p>
