<div class="formstep-form-group">
    <label class="formstep-label">Sở thích</label>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
        <label class="formstep-checkbox-label">
            <input type="checkbox" name="interests[]" value="sports" class="formstep-checkbox"
                   <?= in_array('sports', $stepData['interests'] ?? []) ? 'checked' : '' ?>>
            Thể thao
        </label>
        <label class="formstep-checkbox-label">
            <input type="checkbox" name="interests[]" value="music" class="formstep-checkbox"
                   <?= in_array('music', $stepData['interests'] ?? []) ? 'checked' : '' ?>>
            Âm nhạc
        </label>
        <label class="formstep-checkbox-label">
            <input type="checkbox" name="interests[]" value="travel" class="formstep-checkbox"
                   <?= in_array('travel', $stepData['interests'] ?? []) ? 'checked' : '' ?>>
            Du lịch
        </label>
        <label class="formstep-checkbox-label">
            <input type="checkbox" name="interests[]" value="reading" class="formstep-checkbox"
                   <?= in_array('reading', $stepData['interests'] ?? []) ? 'checked' : '' ?>>
            Đọc sách
        </label>
        <label class="formstep-checkbox-label">
            <input type="checkbox" name="interests[]" value="cooking" class="formstep-checkbox"
                   <?= in_array('cooking', $stepData['interests'] ?? []) ? 'checked' : '' ?>>
            Nấu ăn
        </label>
        <label class="formstep-checkbox-label">
            <input type="checkbox" name="interests[]" value="technology" class="formstep-checkbox"
                   <?= in_array('technology', $stepData['interests'] ?? []) ? 'checked' : '' ?>>
            Công nghệ
        </label>
    </div>
</div>

<div class="formstep-form-group">
    <label class="formstep-label" for="newsletter">Nhận thông báo</label>
    <div>
        <label class="formstep-radio-label">
            <input type="radio" name="newsletter" value="daily" class="formstep-radio"
                   <?= ($stepData['newsletter'] ?? '') === 'daily' ? 'checked' : '' ?>>
            Hàng ngày
        </label>
        <label class="formstep-radio-label">
            <input type="radio" name="newsletter" value="weekly" class="formstep-radio"
                   <?= ($stepData['newsletter'] ?? '') === 'weekly' ? 'checked' : '' ?>>
            Hàng tuần
        </label>
        <label class="formstep-radio-label">
            <input type="radio" name="newsletter" value="monthly" class="formstep-radio"
                   <?= ($stepData['newsletter'] ?? '') === 'monthly' ? 'checked' : '' ?>>
            Hàng tháng
        </label>
        <label class="formstep-radio-label">
            <input type="radio" name="newsletter" value="none" class="formstep-radio"
                   <?= ($stepData['newsletter'] ?? '') === 'none' ? 'checked' : '' ?>>
            Không nhận
        </label>
    </div>
</div>

<div class="formstep-form-group">
    <label class="formstep-label" for="bio">Giới thiệu bản thân</label>
    <textarea id="bio" name="bio" class="formstep-textarea" 
              placeholder="Viết vài dòng về bản thân..." rows="4"><?= htmlspecialchars($stepData['bio'] ?? '') ?></textarea>
</div>

<div class="formstep-form-group">
    <label class="formstep-checkbox-label">
        <input type="checkbox" name="terms" value="1" class="formstep-checkbox" required
               <?= ($stepData['terms'] ?? '') ? 'checked' : '' ?>>
        Tôi đồng ý với <a href="#" style="color: #007bff;">điều khoản sử dụng</a> và <a href="#" style="color: #007bff;">chính sách bảo mật</a> <span style="color: red;">*</span>
    </label>
</div>

<p style="color: #6c757d; font-size: 14px; margin-top: 20px;">
    <strong>Lưu ý:</strong> Bạn có thể thay đổi các tùy chọn này sau khi đăng ký.
</p>
