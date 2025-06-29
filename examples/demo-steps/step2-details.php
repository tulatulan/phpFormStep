<h4>Step 2: Additional Details</h4>
<p class="text-muted">Optional details that can be filled out anytime.</p>

<div class="row">
    <div class="col-md-6">
        <label for="author" class="form-label">Author</label>
        <input type="text" class="form-control" id="author" name="author" 
               value="<?= htmlspecialchars($stepData['author'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label for="publish_date" class="form-label">Publish Date</label>
        <input type="date" class="form-control" id="publish_date" name="publish_date" 
               value="<?= htmlspecialchars($stepData['publish_date'] ?? '') ?>">
    </div>
</div>

<div class="mt-3">
    <label for="tags" class="form-label">Tags</label>
    <input type="text" class="form-control" id="tags" name="tags" 
           value="<?= htmlspecialchars($stepData['tags'] ?? '') ?>">
    <div class="form-text">Separate tags with commas.</div>
</div>

<div class="mt-3">
    <label for="excerpt" class="form-label">Excerpt</label>
    <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?= htmlspecialchars($stepData['excerpt'] ?? '') ?></textarea>
    <div class="form-text">Short description for previews.</div>
</div>

<?php if ($formManager->isStepCompleted(2)): ?>
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle"></i> Step completed! Details saved.
    </div>
<?php endif; ?>
