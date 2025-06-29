<h4>Step 1: Basic Information <span class="text-danger">*</span></h4>
<p class="text-muted">This is the initialization step - required to create the record.</p>

<div class="row">
    <div class="col-md-8">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="title" name="title" 
               value="<?= htmlspecialchars($stepData['title'] ?? '') ?>" required>
        <div class="form-text">This field is required and will create the main record.</div>
    </div>
    <div class="col-md-4">
        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
        <select class="form-control" id="category" name="category" required>
            <option value="">Select category</option>
            <option value="tech" <?= ($stepData['category'] ?? '') === 'tech' ? 'selected' : '' ?>>Technology</option>
            <option value="business" <?= ($stepData['category'] ?? '') === 'business' ? 'selected' : '' ?>>Business</option>
            <option value="lifestyle" <?= ($stepData['category'] ?? '') === 'lifestyle' ? 'selected' : '' ?>>Lifestyle</option>
        </select>
    </div>
</div>

<div class="mt-3">
    <label for="slug" class="form-label">URL Slug</label>
    <input type="text" class="form-control" id="slug" name="slug" 
           value="<?= htmlspecialchars($stepData['slug'] ?? '') ?>">
    <div class="form-text">Leave empty to auto-generate from title.</div>
</div>

<?php if ($formManager->isStepCompleted(1)): ?>
    <div class="alert alert-success mt-3">
        <i class="fas fa-check"></i> Step completed! Record created with ID: <?= $formManager->getPrimaryKeyValue() ?>
    </div>
<?php endif; ?>
