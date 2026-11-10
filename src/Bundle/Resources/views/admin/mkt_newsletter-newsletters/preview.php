<?php

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;

/** @var NewsletterNewsletter $item */
$content = $item->getContent();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($item->getName() ?? 'Newsletter', ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        html, body { margin: 0; padding: 0; height: 100%; font-family: Arial, sans-serif; }
        .newsletter-preview { display: flex; flex-direction: column; height: 100vh; }
        .newsletter-preview-header { padding: 12px; background: #1a2433; color: #fff; }
        .newsletter-preview-header h1 { margin: 0; font-size: 18px; }
        .newsletter-preview-header p { margin: 6px 0 0; font-size: 14px; }
        .newsletter-preview iframe { flex: 1; width: 100%; border: 0; background: #e8ecf0; }
        .newsletter-preview-empty { padding: 24px; }
    </style>
</head>
<body>
<div class="newsletter-preview">
    <header class="newsletter-preview-header">
        <h1><?= htmlspecialchars($item->getName() ?? 'Newsletter', ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if ($item->getSubject()): ?>
            <p><?= htmlspecialchars($item->getSubject(), ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </header>
    <?php if ($content !== null && trim($content) !== ''): ?>
        <iframe title="Newsletter preview" sandbox=""
                srcdoc="<?= htmlspecialchars($content, ENT_QUOTES, 'UTF-8') ?>"></iframe>
    <?php else: ?>
        <p class="newsletter-preview-empty">No saved newsletter content to preview. Save your content in the editor first.</p>
    <?php endif; ?>
</div>
</body>
</html>
