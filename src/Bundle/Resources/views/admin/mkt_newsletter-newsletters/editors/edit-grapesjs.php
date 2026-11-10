<?php

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;

/** @var NewsletterNewsletter $item */
$editorData = $item->getEditorData() ?: '{}';
?>
    <!-- GrapesJS 0.21 + Newsletter Preset (email-compatible pair) -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs@0.21.13/dist/css/grapes.min.css"/>
    <link rel="stylesheet" href="https://unpkg.com/grapesjs-preset-newsletter@1.0.1/dist/index.css"/>
    <style>
        #editor-wrap, #editor-wrap *, #editor-wrap *::before, #editor-wrap *::after { box-sizing: border-box; }

        #editor-wrap { display: flex; flex-direction: column; height: 100vh; font-family: Arial, sans-serif; }
        #newsletter-name { width: 220px; max-width: 100%; padding: 5px 8px; border: 1px solid #7f8c8d; border-radius: 3px; color: #1a2433; background: #fff; }

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
<div id="editor-wrap">
    <div id="editor-toolbar">
        <div class="toolbar-title">
            <label for="newsletter-name">Newsletter name</label>
            <input id="newsletter-name" name="name" type="text"
                   value="<?= htmlspecialchars($item->getName() ?? '', ENT_QUOTES, 'UTF-8') ?>"/>
            <?php if ($item->getSubject()): ?>
                <span>&nbsp;·&nbsp;<?= htmlspecialchars($item->getSubject()) ?></span>
            <?php endif; ?>
        </div>

        <div class="toolbar-sep"></div>

        <button type="button" id="btn-desktop" class="active" title="Desktop view (600px)">🖥 Desktop</button>
        <button type="button" id="btn-mobile" title="Mobile view (320px)">📱 Mobile</button>

        <div class="toolbar-sep"></div>

        <span id="save-status"></span>
        <button type="button" id="back-btn" onclick="history.back()">← Back</button>
        <button type="button" id="save-btn">💾 Save</button>
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

    /* ── Basic email-safe building blocks ───────────────────────────── */
    [
        {
            id: 'email-row',
            label: 'Row',
            icon: '<rect x="3" y="6" width="26" height="20" rx="2"/>',
            content: '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding:10px 0;"></td></tr></tbody></table>',
        },
        {
            id: 'email-two-columns',
            label: '2 Columns',
            icon: '<rect x="3" y="6" width="26" height="20" rx="2"/><path d="M16 6v20"/>',
            content: '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="50%" valign="top" style="padding:10px;"></td><td width="50%" valign="top" style="padding:10px;"></td></tr></tbody></table>',
        },
        {
            id: 'email-heading',
            label: 'Heading',
            icon: '<path d="M7 6v20M25 6v20M7 16h18"/>',
            content: '<h2 style="margin:0 0 16px;font-family:Arial,Helvetica,sans-serif;font-size:24px;line-height:30px;color:#1a2433;">Heading</h2>',
        },
        {
            id: 'email-text',
            label: 'Text',
            icon: '<path d="M4 7h24M4 13h24M4 19h24M4 25h16"/>',
            content: '<p style="margin:0 0 16px;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:24px;color:#333333;">Write your text here.</p>',
        },
        {
            id: 'email-button',
            label: 'Button',
            icon: '<rect x="3" y="9" width="26" height="14" rx="4"/><path d="M10 16h12"/>',
            content: '<table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center"><tbody><tr><td align="center" bgcolor="#2980b9" style="border-radius:3px;"><a href="#" style="display:inline-block;padding:12px 28px;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;">Button text</a></td></tr></tbody></table>',
        },
        {
            id: 'email-divider',
            label: 'Divider',
            icon: '<path d="M3 16h26M8 7h16M8 25h16"/>',
            content: '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding:12px 0;border-bottom:1px solid #e0e0e0;font-size:1px;line-height:1px;">&nbsp;</td></tr></tbody></table>',
        },
        {
            id: 'email-spacer',
            label: 'Spacer',
            icon: '<path d="M3 5h26M3 27h26M16 9v14m-4-10 4-4 4 4m-8 6 4 4 4-4"/>',
            content: '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td height="24" style="height:24px;font-size:1px;line-height:24px;">&nbsp;</td></tr></tbody></table>',
        },
        {
            id: 'email-three-columns',
            label: '3 Columns',
            icon: '<rect x="2" y="6" width="28" height="20" rx="2"/><path d="M11 6v20M21 6v20"/>',
            content: '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="33%" valign="top" style="padding:10px;">Column 1</td><td width="34%" valign="top" style="padding:10px;">Column 2</td><td width="33%" valign="top" style="padding:10px;">Column 3</td></tr></tbody></table>',
        },
        {
            id: 'email-image',
            label: 'Image',
            icon: '<rect x="3" y="4" width="26" height="24" rx="2"/><circle cx="10" cy="11" r="3"/><path d="m3 25 9-9 5 5 5-9 7 13"/>',
            content: '<img src="https://placehold.co/600x240/png" alt="Describe your image" width="600" style="display:block;width:100%;max-width:600px;height:auto;border:0;"/>',
        },
        {
            id: 'email-image-text',
            label: 'Image + Text',
            category: 'Sections',
            icon: '<rect x="2" y="7" width="12" height="18"/><path d="M18 9h12M18 16h12M18 23h9M3 22l5-6 5 6"/>',
            content: '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="40%" valign="top" style="padding:12px;"><img src="https://placehold.co/240x180/png" alt="Describe your image" width="240" style="display:block;width:100%;max-width:240px;height:auto;border:0;"/></td><td width="60%" valign="top" style="padding:12px;font-family:Arial,Helvetica,sans-serif;color:#333333;"><h2 style="margin:0 0 12px;font-size:22px;line-height:28px;">Featured story</h2><p style="margin:0;font-size:16px;line-height:24px;">Share your latest news here.</p></td></tr></tbody></table>',
        },
        {
            id: 'email-header',
            label: 'Header',
            category: 'Sections',
            icon: '<rect x="3" y="4" width="26" height="24" rx="2"/><path d="M3 13h26M9 8h14"/>',
            content: '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" bgcolor="#1a2433" style="padding:28px;font-family:Arial,Helvetica,sans-serif;color:#ffffff;"><h1 style="margin:0;font-size:28px;line-height:36px;">Your newsletter</h1><p style="margin:8px 0 0;font-size:14px;line-height:22px;">The latest news from our team</p></td></tr></tbody></table>',
        },
        {
            id: 'email-quote',
            label: 'Quote',
            category: 'Sections',
            icon: '<path d="M5 8h9v10H8v6H4v-9l1-7zm15 0h9v10h-6v6h-4v-9l1-7z"/>',
            content: '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding:20px;border-left:4px solid #2980b9;background-color:#f5f7fa;font-family:Arial,Helvetica,sans-serif;color:#333333;"><p style="margin:0 0 12px;font-size:18px;line-height:28px;font-style:italic;">&ldquo;Add an inspiring quote or customer testimonial.&rdquo;</p><p style="margin:0;font-size:14px;line-height:22px;">&mdash; Author name</p></td></tr></tbody></table>',
        },
        {
            id: 'email-social',
            label: 'Social Links',
            category: 'Sections',
            icon: '<circle cx="7" cy="16" r="4"/><circle cx="25" cy="7" r="4"/><circle cx="25" cy="25" r="4"/><path d="m11 14 10-5M11 18l10 5"/>',
            content: '<table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center"><tbody><tr><td style="padding:12px;font-family:Arial,Helvetica,sans-serif;font-size:14px;"><a href="https://www.facebook.com/" style="color:#2980b9;">Facebook</a></td><td style="padding:12px;font-family:Arial,Helvetica,sans-serif;font-size:14px;"><a href="https://www.instagram.com/" style="color:#2980b9;">Instagram</a></td><td style="padding:12px;font-family:Arial,Helvetica,sans-serif;font-size:14px;"><a href="https://www.linkedin.com/" style="color:#2980b9;">LinkedIn</a></td></tr></tbody></table>',
        },
        {
            id: 'email-footer',
            label: 'Footer',
            category: 'Sections',
            icon: '<rect x="3" y="4" width="26" height="24" rx="2"/><path d="M3 19h26M9 24h14"/>',
            content: '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" bgcolor="#f5f7fa" style="padding:24px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:20px;color:#888888;"><p style="margin:0 0 8px;">Your company &middot; Your mailing address</p><p style="margin:0;">You received this email because you subscribed to our newsletter.</p><p style="margin:8px 0 0;"><a href="#unsubscribe" style="color:#888888;">Unsubscribe</a></p></td></tr></tbody></table>',
        },
    ].forEach(function (block) {
        editor.BlockManager.add(block.id, {
            label: block.label,
            media: '<svg viewBox="0 0 32 32" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">' + block.icon + '</svg>',
            category: block.category || 'Basic',
            content: block.content,
        });
    });

    /* ── Load existing content ───────────────────────────────────────── */
    var editorData = <?= json_encode($editorData) ?>;
    if (editorData && editorData !== '{}') {
        try {
            editor.loadProjectData(JSON.parse(editorData));
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
    document.getElementById('newsletter-name').addEventListener('input', function () { isDirty = true; });
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

    /* ── Email HTML builder ─────────────────────────────────────────── */
    function buildEmailHtml() {
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
        return [
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
    }

    /* ── Bridge used by the shared "Edit HTML source" panel ──────────── */
    window.newsletterEditorBridge = {
        getHtml: function (callback) { callback(buildEmailHtml()); },
        setHtml: function (html) {
            /* Only the <body> (plus any <style> blocks) is editable content. */
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var styles = Array.prototype.map.call(
                doc.querySelectorAll('head style'),
                function (style) { return style.outerHTML; }
            ).join('\n');
            editor.setStyle('');
            editor.setComponents(styles + doc.body.innerHTML);
        },
        onChange: function (callback) { editor.on('update', callback); },
        save: function () { document.getElementById('save-btn').click(); }
    };

    /* ── Save handler ────────────────────────────────────────────────── */
    document.getElementById('save-btn').addEventListener('click', function () {
        var saveBtn = this;
        var statusEl = document.getElementById('save-status');
        saveBtn.disabled = true;
        statusEl.style.color = '#bdc3c7';
        statusEl.textContent = 'Saving…';

        /* Project JSON (used to restore the editable state) */
        var projectData = JSON.stringify(editor.getProjectData());
        var emailHtml = buildEmailHtml();

        var formData = new FormData();
        formData.append('name', document.getElementById('newsletter-name').value);
        formData.append('editor_data', projectData);
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
