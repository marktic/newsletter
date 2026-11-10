<div class="card h-100">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <h5 class="card-title mb-0"><?= $escape($item->getName() ?? 'Newsletter') ?></h5>
            <?php if ($item->getSubject()): ?>
                <small class="text-muted"><?= $escape($item->getSubject()) ?></small>
            <?php endif; ?>
        </div>
        <?php if (!empty($urls['preview'])): ?>
            <a href="<?= $escape($urls['preview']) ?>" target="_blank" rel="noopener noreferrer"
               class="btn btn-sm btn-outline-secondary">
                <?= $escape($trans('preview.open_new_tab')) ?>
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if ($hasContent): ?>
            <iframe title="Newsletter preview" sandbox=""
                    style="width: 100%; min-height: 720px; border: 0; background: #e8ecf0;"
                    srcdoc="<?= $escape($content) ?>"></iframe>
        <?php else: ?>
            <p class="p-4 mb-0">No saved newsletter content to preview. Save your content in the editor first.</p>
        <?php endif; ?>
    </div>
</div>
