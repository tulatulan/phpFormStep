<div class="formstep-form-group">
    <label class="formstep-label" for="name">Họ và tên <span style="color: red;">*</span></label>
    <input type="text" id="name" name="name" class="formstep-input" 
           value="<?= htmlspecialchars($stepData['name'] ?? '') ?>" 
           placeholder="Nhập họ và tên đầy đủ" required>
</div>

<div class="formstep-form-group">
    <label class="formstep-label" for="email">Email <span style="color: red;">*</span></label>
    <input type="email" id="email" name="email" class="formstep-input" 
           value="<?= htmlspecialchars($stepData['email'] ?? '') ?>" 
           placeholder="example@email.com" required>
</div>

<div class="formstep-form-group">
    <label class="formstep-label" for="birth_date">Ngày sinh</label>
    <input type="date" id="birth_date" name="birth_date" class="formstep-input" 
           value="<?= htmlspecialchars($stepData['birth_date'] ?? '') ?>">
</div>

<div class="formstep-form-group">
    <label class="formstep-label" for="gender">Giới tính</label>
    <select id="gender" name="gender" class="formstep-select">
        <option value="">Chọn giới tính</option>
        <option value="male" <?= ($stepData['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Nam</option>
        <option value="female" <?= ($stepData['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Nữ</option>
        <option value="other" <?= ($stepData['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Khác</option>
    </select>
</div>

<p style="color: #6c757d; font-size: 14px; margin-top: 20px;">
    <strong>Lưu ý:</strong> Thông tin này sẽ được lưu lại và sử dụng cho các bước tiếp theo.
</p>
