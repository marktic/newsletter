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

        /* Email preset: adds table-based blocks, inline CSS, 600px canvas */
        plugins: ['gjs-preset-newsletter'],
        pluginsOpts: {
            'gjs-preset-newsletter': {
                /* Inline all CSS so email clients don't strip <style> blocks */
                inlineCss: true,
                /* Starter import placeholder shown in the "Import" modal */
                importPlaceholder:
                    '<table class="main"><tr><td></td></tr></table>',
                /* Labels */
                modalLabelImport: 'Paste your HTML template below and click Import',
                modalLabelExport: 'Copy the code and use it in your mailer',
            }
        },

        /* Canvas: simulate a typical email client background */
        canvas: {
            styles: [
                /* Google Fonts for use inside the canvas */
                'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap'
            ]
        },

        /* Style manager: only properties that render reliably in email clients */
        styleManager: {
            sectors: [
                {
                    name: 'General',
                    open: false,
                    properties: [
                        { name: 'Width', property: 'width', type: 'integer', units: ['px', '%'] },
                        { name: 'Height', property: 'height', type: 'integer', units: ['px'] },
                    ]
                },
                {
                    name: 'Typography',
                    open: true,
                    properties: [
                        { name: 'Font', property: 'font-family', type: 'select',
                          options: [
                            {value: 'Arial, Helvetica, sans-serif', name: 'Arial'},
                            {value: "'Open Sans', Arial, sans-serif", name: 'Open Sans'},
                            {value: 'Georgia, serif', name: 'Georgia'},
                            {value: "'Trebuchet MS', sans-serif", name: 'Trebuchet'},
                            {value: 'Verdana, Geneva, sans-serif', name: 'Verdana'},
                          ]
                        },
                        { name: 'Size',   property: 'font-size',   type: 'integer', units: ['px'] },
                        { name: 'Weight', property: 'font-weight', type: 'select',
                          options: [
                            {value: '400', name: 'Normal'},
                            {value: '600', name: 'Semi-bold'},
                            {value: '700', name: 'Bold'},
                          ]
                        },
                        { name: 'Line height',  property: 'line-height',   type: 'integer', units: ['px'] },
                        { name: 'Color',        property: 'color',         type: 'color' },
                        { name: 'Text align',   property: 'text-align',    type: 'radio',
                          options: [
                            {value: 'left',   name: '«'},
                            {value: 'center', name: '='},
                            {value: 'right',  name: '»'},
                          ]
                        },
                    ]
                },
                {
                    name: 'Spacing',
                    open: false,
                    properties: [
                        { name: 'Padding',    property: 'padding',    type: 'integer', units: ['px'] },
                        { name: 'Margin',     property: 'margin',     type: 'integer', units: ['px'] },
                    ]
                },
                {
                    name: 'Background',
                    open: false,
                    properties: [
                        { name: 'Background color', property: 'background-color', type: 'color' },
                    ]
                },
                {
                    name: 'Border',
                    open: false,
                    properties: [
                        { name: 'Border',       property: 'border',        type: 'integer', units: ['px'] },
                        { name: 'Border color', property: 'border-color',  type: 'color' },
                        { name: 'Border style', property: 'border-style',  type: 'select',
                          options: [
                            {value: 'none',   name: 'None'},
                            {value: 'solid',  name: 'Solid'},
                            {value: 'dashed', name: 'Dashed'},
                          ]
                        },
                    ]
                },
            ]
        },

        /* Panels: hide panels we don't need for email editing */
        panels: { defaults: [] },
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
        if (existingHtml) { editor.setComponents(existingHtml); }
    }

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
