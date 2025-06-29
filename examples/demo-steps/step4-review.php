<h4>Step 4: Review & Publish</h4>
<p class="text-muted">Review all information and publish your content.</p>

<?php $allData = $formManager->getAllStepData(); ?>

<div class="row">
    <div class="col-md-6">
        <h6>Basic Information</h6>
        <table class="table table-sm">
            <tr><td><strong>Title:</strong></td><td><?= htmlspecialchars($allData[1]['title'] ?? 'Not set') ?></td></tr>
            <tr><td><strong>Category:</strong></td><td><?= htmlspecialchars($allData[1]['category'] ?? 'Not set') ?></td></tr>
            <tr><td><strong>Slug:</strong></td><td><?= htmlspecialchars($allData[1]['slug'] ?? 'Not set') ?></td></tr>
        </table>
        
        <h6>Details</h6>
        <table class="table table-sm">
            <tr><td><strong>Author:</strong></td><td><?= htmlspecialchars($allData[2]['author'] ?? 'Not set') ?></td></tr>
            <tr><td><strong>Publish Date:</strong></td><td><?= htmlspecialchars($allData[2]['publish_date'] ?? 'Not set') ?></td></tr>
            <tr><td><strong>Tags:</strong></td><td><?= htmlspecialchars($allData[2]['tags'] ?? 'Not set') ?></td></tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6>Content</h6>
        <div class="border p-2 mb-3" style="max-height: 200px; overflow-y: auto;">
            <?= nl2br(htmlspecialchars(substr($allData[3]['content'] ?? 'No content', 0, 500))) ?>
            <?php if (strlen($allData[3]['content'] ?? '') > 500): ?>
                <em>... (truncated)</em>
            <?php endif; ?>
        </div>
        
        <h6>SEO</h6>
        <table class="table table-sm">
            <tr><td><strong>Meta Title:</strong></td><td><?= htmlspecialchars($allData[3]['meta_title'] ?? 'Not set') ?></td></tr>
            <tr><td><strong>Meta Description:</strong></td><td><?= htmlspecialchars($allData[3]['meta_description'] ?? 'Not set') ?></td></tr>
        </table>
    </div>
</div>

<div class="mt-4">
    <h6>Publish Settings</h6>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="featured" value="1" 
               <?= ($stepData['featured'] ?? false) ? 'checked' : '' ?>>
        <label class="form-check-label">Feature this article</label>
    </div>
    
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="send_notification" value="1" 
               <?= ($stepData['send_notification'] ?? false) ? 'checked' : '' ?>>
        <label class="form-check-label">Send notification to subscribers</label>
    </div>
    
    <div class="mt-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-control" name="status">
            <option value="draft" <?= ($stepData['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= ($stepData['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
            <option value="scheduled" <?= ($stepData['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
        </select>
    </div>
</div>

<?php if ($formManager->isStepCompleted(4)): ?>
    <div class="alert alert-success mt-3">
        <i class="fas fa-check"></i> Article published successfully!
    </div>
<?php endif; ?>
