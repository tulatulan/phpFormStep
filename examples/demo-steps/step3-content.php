<h4>Step 3: Content <span class="text-danger">*</span></h4>
<p class="text-muted">Main content - required to be saved before proceeding.</p>

<div class="mb-3">
    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
    <textarea class="form-control" id="content" name="content" rows="10" required><?= htmlspecialchars($stepData['content'] ?? '') ?></textarea>
    <div class="form-text">Minimum 20 characters required.</div>
</div>

<div class="row">
    <div class="col-md-6">
        <label for="meta_title" class="form-label">SEO Title</label>
        <input type="text" class="form-control" id="meta_title" name="meta_title" 
               value="<?= htmlspecialchars($stepData['meta_title'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label for="meta_keywords" class="form-label">SEO Keywords</label>
        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
               value="<?= htmlspecialchars($stepData['meta_keywords'] ?? '') ?>">
    </div>
</div>

<div class="mt-3">
    <label for="meta_description" class="form-label">SEO Description</label>
    <textarea class="form-control" id="meta_description" name="meta_description" rows="2"><?= htmlspecialchars($stepData['meta_description'] ?? '') ?></textarea>
</div>

<?php if ($formManager->isStepCompleted(3)): ?>
    <div class="alert alert-success mt-3">
        <i class="fas fa-check"></i> Step completed! Content saved.
    </div>
<?php endif; ?>
