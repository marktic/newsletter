<?php

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;

/**
 * Unlayer email editor integration.
 *
 * Required config (config/mkt_newsletter.php):
 *   'editor' => [
 *       'driver'  => 'unlayer',
 *       'options' => [
 *           'project_id' => 12345,   // (optional) Unlayer project ID
 *       ],
 *   ],
 *
 * @var NewsletterNewsletter $item
 * @var array                $editorOptions  Values from editor.options config key
 */
$editorData   = $item->getEditorData() ?: '{}';
$projectId    = (int)($editorOptions['project_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Newsletter Editor: <?= htmlspecialchars($item->getName() ?? '') ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100%; font-family: Arial, sans-serif; }

        #editor-wrap { display: flex; flex-direction: column; height: 100vh; }

        /* ── Toolbar ──────────────────────────────────────────────────────── */
        #editor-toolbar {
            display: flex; align-items: center; flex-wrap: wrap; gap: 6px;
            padding: 8px 12px; background: #1a2433; color: #fff; flex-shrink: 0;
            border-bottom: 2px solid #0d1a26;
        }
        .toolbar-title { font-weight: bold; font-size: 14px; flex: 1; min-width: 120px; }
        .toolbar-title span { font-weight: normal; opacity: .7; font-size: 12px; }
        .toolbar-sep { width: 1px; height: 24px; background: rgba(255,255,255,.2); margin: 0 2px; }
        #editor-toolbar button {
            padding: 5px 12px; border: none; border-radius: 3px;
            cursor: pointer; font-size: 12px; font-weight: 600; letter-spacing: .3px;
        }
        #save-btn { background: #27ae60; color: #fff; }
        #save-btn:hover { background: #219a52; }
        #save-btn:disabled { background: #7f8c8d; cursor: default; }
        #back-btn { background: #7f8c8d; color: #fff; }
        #back-btn:hover { background: #6c7a7d; }
        #save-status { font-size: 12px; min-width: 80px; }

        /* ── Unlayer container ────────────────────────────────────────────── */
        #unlayer-editor { flex: 1; }
    </style>
</head>
<body>
<div id="editor-wrap">
    <div id="editor-toolbar">
        <div class="toolbar-title">
            ✉&nbsp;<?= htmlspecialchars($item->getName() ?? 'Newsletter') ?>
            <?php if ($item->getSubject()): ?>
                <span>&nbsp;·&nbsp;<?= htmlspecialchars($item->getSubject()) ?></span>
            <?php endif; ?>
        </div>

        <div class="toolbar-sep"></div>

        <span id="save-status"></span>
        <button id="back-btn" onclick="history.back()">← Back</button>
        <button id="save-btn">💾 Save</button>
    </div>

    <div id="unlayer-editor"></div>
</div>

<script src="https://editor.unlayer.com/embed.js"></script>
<script>
    /* ── Editor initialisation ───────────────────────────────────────── */
    var initOptions = {
        id: 'unlayer-editor',
        displayMode: 'email',
        appearance: { theme: 'dark' },
    };

    <?php if ($projectId > 0): ?>
    initOptions.projectId = <?= json_encode($projectId) ?>;
    <?php endif; ?>

    unlayer.init(initOptions);

    /* ── Load existing design ────────────────────────────────────────── */
    var editorData = <?= json_encode($editorData) ?>;
    if (editorData && editorData !== '{}') {
        try {
            unlayer.loadDesign(JSON.parse(editorData));
        } catch (e) {
            /* If loading the stored design fails, the editor starts blank */
            console.warn('Could not load saved Unlayer design:', e);
        }
    }

    /* ── Track unsaved changes ───────────────────────────────────────── */
    var isDirty = false;
    unlayer.addEventListener('design:updated', function () { isDirty = true; });
    window.addEventListener('beforeunload', function (e) {
        if (isDirty) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    /* ── Ctrl+S / Cmd+S keyboard shortcut ───────────────────────────── */
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            document.getElementById('save-btn').click();
        }
    });

    /* ── Save handler ────────────────────────────────────────────────── */
    document.getElementById('save-btn').addEventListener('click', function () {
        var saveBtn = this;
        var statusEl = document.getElementById('save-status');
        saveBtn.disabled = true;
        statusEl.style.color = '#bdc3c7';
        statusEl.textContent = 'Saving…';

        unlayer.exportHtml(function (data) {
            var formData = new FormData();
            formData.append('editor_data', JSON.stringify(data.design));
            formData.append('content', data.html);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(function (response) {
                saveBtn.disabled = false;
                if (response.ok) {
                    isDirty = false;
                    statusEl.style.color = '#2ecc71';
                    statusEl.textContent = '✓ Saved';
                    setTimeout(function () { statusEl.textContent = ''; }, 3000);
                } else {
                    statusEl.style.color = '#e74c3c';
                    statusEl.textContent = '✗ Error';
                }
            }).catch(function () {
                saveBtn.disabled = false;
                statusEl.style.color = '#e74c3c';
                statusEl.textContent = '✗ Error';
            });
        });
    });
</script>
</body>
</html>
