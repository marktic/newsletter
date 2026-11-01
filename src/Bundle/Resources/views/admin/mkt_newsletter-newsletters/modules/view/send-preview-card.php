<?php if (!empty($urls['sendPreview'])): ?>
    <div class="card mb-4 mkt-newsletter-send-preview">
        <div class="card-header"><h5 class="card-title mb-0"><?= $escape($trans('preview_email.title')) ?></h5></div>
        <div class="card-body">
            <form method="post" action="<?= $escape($urls['sendPreview']) ?>">
                <div class="mb-2">
                    <label class="form-label" for="mkt-newsletter-preview-emails"><?= $escape($trans('preview_email.emails')) ?></label>
                    <input type="text" class="form-control" id="mkt-newsletter-preview-emails" name="emails"
                           placeholder="you@example.com" required/>
                    <div class="form-text"><?= $escape($trans('preview_email.emails_help')) ?></div>
                </div>
                <button type="submit" class="btn btn-success btn-sm" <?= $hasContent ? '' : 'disabled' ?>>
                    <?= $escape($trans('preview_email.send')) ?>
                </button>
            </form>
        </div>
    </div>
<?php endif; ?>
