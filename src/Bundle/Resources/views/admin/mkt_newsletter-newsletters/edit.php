<?php

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;

/** @var NewsletterNewsletter $item */
$grapesjsData = $item->getGrapesjsData() ?: '{}';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Newsletter Editor: <?= htmlspecialchars($item->getName() ?? '') ?></title>
    <!-- GrapesJS 0.21 + Newsletter Preset (email-compatible pair) -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs@0.21.13/dist/css/grapes.min.css"/>
    <link rel="stylesheet" href="https://unpkg.com/grapesjs-preset-newsletter@1.0.1/dist/index.css"/>
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
        #btn-desktop { background: #34495e; color: #ecf0f1; }
        #btn-mobile  { background: #34495e; color: #ecf0f1; }
        #btn-desktop.active, #btn-mobile.active { background: #2980b9; color: #fff; }
        #save-btn { background: #27ae60; color: #fff; }
        #save-btn:hover { background: #219a52; }
        #save-btn:disabled { background: #7f8c8d; cursor: default; }
        #back-btn { background: #7f8c8d; color: #fff; }
        #back-btn:hover { background: #6c7a7d; }
        #save-status { font-size: 12px; min-width: 80px; }

        /* ── GrapesJS container ───────────────────────────────────────────── */
        #gjs { flex: 1; overflow: hidden; }

        /* Give the canvas a light email-client-like background */
        .gjs-cv-canvas { background-color: #e8ecf0 !important; }
        .gjs-frame-wrapper { background: #fff; }
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

        <button id="btn-desktop" class="active" title="Desktop view (600px)">🖥 Desktop</button>
        <button id="btn-mobile" title="Mobile view (320px)">📱 Mobile</button>

        <div class="toolbar-sep"></div>

        <span id="save-status"></span>
        <button id="back-btn" onclick="history.back()">← Back</button>
        <button id="save-btn">💾 Save</button>
    </div>

    <div id="gjs"></div>
</div>

<script src="https://unpkg.com/grapesjs@0.21.13/dist/grapes.min.js"></script>
<script src="https://unpkg.com/grapesjs-preset-newsletter@1.0.1/dist/index.js"></script>
<script>
    /* ── Editor initialisation ───────────────────────────────────────── */
    var editor = grapesjs.init({
        container: '#gjs',
        height: '100%',
        storageManager: false,

        /* Email preset: adds table-based blocks, inline CSS, Desktop/Mobile
           devices (600px / 320px), and juice-based CSS inliner command */
        plugins: ['gjs-preset-newsletter'],
        pluginsOpts: {
            'gjs-preset-newsletter': {
                /* Inline all CSS so email clients render styles correctly */
                inlineCss: true,
                /* Placeholder shown in the Import HTML modal */
                importPlaceholder: [
                    '<table width="100%" style="background:#e8ecf0;padding:20px 0">',
                    '  <tr><td align="center">',
                    '    <table width="600" style="background:#ffffff;border-radius:4px">',
                    '      <tr><td style="padding:20px">Your content here</td></tr>',
                    '    </table>',
                    '  </td></tr>',
                    '</table>',
                ].join('\n'),
                modalLabelImport: 'Paste your HTML template and click Import',
                modalLabelExport: 'Copy the HTML below to use in your mailer',
            }
        },

        /* Canvas: load web-safe fonts for use inside the email preview */
        canvas: {
            styles: [
                'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap',
            ]
        },

        /* NOTE: do NOT set panels:{defaults:[]} here.
           The preset-newsletter plugin adds its own panel buttons (Blocks,
           Layers, Style, Settings, Import, Export) to the standard panel
           containers. Wiping defaults removes those containers and leaves
           the editor without any UI controls. */
    });

    /* ── Load existing content ───────────────────────────────────────── */
    var grapesjsData = <?= json_encode($grapesjsData) ?>;
    if (grapesjsData && grapesjsData !== '{}') {
        try {
            editor.loadProjectData(JSON.parse(grapesjsData));
        } catch (e) {
            var existingHtml = <?= json_encode($item->getContent() ?: '') ?>;
            if (existingHtml) { editor.setComponents(existingHtml); }
        }
    } else {
        var existingHtml = <?= json_encode($item->getContent() ?: '') ?>;
        if (existingHtml) {
            editor.setComponents(existingHtml);
        } else {
            /* Default starter template for a brand-new newsletter */
            editor.setComponents([
                '<table width="100%" style="background-color:#e8ecf0;padding:20px 0;margin:0;">',
                '  <tr><td align="center">',
                '    <table width="600" style="background-color:#ffffff;border-radius:4px;overflow:hidden;">',
                '      <!-- Header -->',
                '      <tr>',
                '        <td align="center" style="background-color:#1a2433;padding:24px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:24px;font-weight:bold;">',
                '          Your Newsletter Title',
                '        </td>',
                '      </tr>',
                '      <!-- Body text -->',
                '      <tr>',
                '        <td style="padding:32px 40px;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:24px;color:#333333;">',
                '          <p style="margin:0 0 16px 0;">Hello,</p>',
                '          <p style="margin:0 0 16px 0;">Write your newsletter content here. Use the blocks panel on the right to add images, buttons, columns, and more.</p>',
                '        </td>',
                '      </tr>',
                '      <!-- CTA Button -->',
                '      <tr>',
                '        <td align="center" style="padding:0 40px 32px 40px;">',
                '          <a href="#" style="display:inline-block;background-color:#2980b9;color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;text-decoration:none;padding:12px 28px;border-radius:3px;">Read More</a>',
                '        </td>',
                '      </tr>',
                '      <!-- Footer -->',
                '      <tr>',
                '        <td align="center" style="background-color:#f5f7fa;padding:20px 40px;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#888888;line-height:20px;border-top:1px solid #e0e0e0;">',
                '          <p style="margin:0 0 6px 0;">You are receiving this email because you subscribed to our newsletter.</p>',
                '          <p style="margin:0;"><a href="#unsubscribe" style="color:#888888;">Unsubscribe</a></p>',
                '        </td>',
                '      </tr>',
                '    </table>',
                '  </td></tr>',
                '</table>',
            ].join('\n'));
        }
    }

    /* ── Track unsaved changes ───────────────────────────────────────── */
    var isDirty = false;
    editor.on('change:changesCount', function () { isDirty = true; });
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

    /* ── Device switching ────────────────────────────────────────────── */
    document.getElementById('btn-desktop').addEventListener('click', function () {
        editor.setDevice('Desktop');
        document.getElementById('btn-desktop').classList.add('active');
        document.getElementById('btn-mobile').classList.remove('active');
    });
    document.getElementById('btn-mobile').addEventListener('click', function () {
        editor.setDevice('Mobile');
        document.getElementById('btn-mobile').classList.add('active');
        document.getElementById('btn-desktop').classList.remove('active');
    });

    /* ── Save handler ────────────────────────────────────────────────── */
    document.getElementById('save-btn').addEventListener('click', function () {
        var saveBtn = this;
        var statusEl = document.getElementById('save-status');
        saveBtn.disabled = true;
        statusEl.style.color = '#bdc3c7';
        statusEl.textContent = 'Saving…';

        /* Project JSON (used to restore the editable state) */
        var projectData = JSON.stringify(editor.getProjectData());

        /* Email-compatible HTML: inline CSS via the newsletter preset's
           built-in juice inliner, then wrap in a proper email document. */
        var bodyHtml = editor.runCommand('gjs-get-inlined-html') || '';
        if (!bodyHtml) {
            /* Fallback: assemble manually */
            var css = editor.getCss({ avoidProtected: true }) || '';
            var html = editor.getHtml({ cleanId: true }) || '';
            bodyHtml = '<style type="text/css">\n' + css + '\n</style>\n' + html;
        }

        /* Build a full, email-client-ready HTML document */
        var emailHtml = [
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"',
            '  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
            '<html xmlns="http://www.w3.org/1999/xhtml" lang="en">',
            '<head>',
            '  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>',
            '  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>',
            '  <meta name="x-apple-disable-message-reformatting"/>',
            '  <!--[if !mso]><!-->',
            '  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>',
            '  <!--<![endif]-->',
            '  <title>' + <?= json_encode(htmlspecialchars($item->getSubject() ?? $item->getName() ?? '')) ?> + '</title>',
            '</head>',
            '<body style="margin:0;padding:0;background-color:#e8ecf0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">',
            bodyHtml,
            '</body>',
            '</html>',
        ].join('\n');

        var formData = new FormData();
        formData.append('grapesjs_data', projectData);
        formData.append('content', emailHtml);

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
</script>
</body>
</html>
