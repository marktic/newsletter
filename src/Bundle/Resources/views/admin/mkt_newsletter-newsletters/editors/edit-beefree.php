<?php

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;

/**
 * Beefree SDK email editor integration.
 *
 * Required config (config/mkt_newsletter.php):
 *   'editor' => [
 *       'driver'  => 'beefree',
 *       'options' => [
 *           'client_id'     => 'YOUR_CLIENT_ID',      // (required) Beefree app credentials
 *           'client_secret' => 'YOUR_CLIENT_SECRET',  // (required)
 *       ],
 *   ],
 *
 * Obtain credentials at https://developers.beefree.io/
 *
 * @var NewsletterNewsletter $item
 * @var array                $editorOptions  Values from editor.options config key
 */
$editorData   = $item->getEditorData();
$clientId     = $editorOptions['client_id']     ?? '';
$clientSecret = $editorOptions['client_secret'] ?? '';
?>
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
        #save-btn { background: #27ae60; color: #fff; }
        #save-btn:hover { background: #219a52; }
        #save-btn:disabled { background: #7f8c8d; cursor: default; }
        #back-btn { background: #7f8c8d; color: #fff; }
        #back-btn:hover { background: #6c7a7d; }
        #save-status { font-size: 12px; min-width: 80px; }

        /* ── Beefree container ────────────────────────────────────────────── */
        #beefree-editor { flex: 1; }
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

        <span id="save-status"></span>
        <button type="button" id="back-btn" onclick="history.back()">← Back</button>
        <button type="button" id="save-btn">💾 Save</button>
    </div>

    <div id="beefree-editor"></div>
</div>

<script src="https://app-rsrc.getbee.io/plugin/BeePlugin.js"></script>
<script>
    /* ── Editor initialisation ───────────────────────────────────────── */
    var isDirty      = false;
    document.getElementById('newsletter-name').addEventListener('input', function () { isDirty = true; });
    var beeInstance  = null;
    var pendingHtml  = null;
    var pendingJson  = null;

    /* Beefree requires server-side auth token exchange. The credentials
       below are passed from the PHP config; the actual token fetch must be
       handled by your backend before the SDK is loaded. See the Beefree
       docs for the recommended server-side token endpoint pattern. */
    var beeConfig = {
        uid:      'newsletter-' + <?= json_encode((string)$item->getId()) ?>,
        container: 'beefree-editor',
        autosave: 0,
        onChange: function () { isDirty = true; },
        onSave: function (jsonFile, htmlFile) {
            pendingJson = jsonFile;
            pendingHtml = htmlFile;
            persistSave(jsonFile, htmlFile);
        },
    };

    /* Load existing design or start blank ─────────────────────────── */
    var savedDesign = <?= json_encode($editorData) ?>;

    BeePlugin.create(
        <?= json_encode($clientId) ?>,
        <?= json_encode($clientSecret) ?>,
        beeConfig,
        function (instance) {
            beeInstance = instance;
            if (savedDesign) {
                try {
                    instance.load(JSON.parse(savedDesign));
                } catch (e) {
                    console.warn('Could not load saved Beefree design:', e);
                    instance.load(null);
                }
            } else {
                instance.load(null);
            }
        }
    );

    /* ── Unsaved-changes guard ───────────────────────────────────────── */
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

    /* ── Save button ─────────────────────────────────────────────────── */
    document.getElementById('save-btn').addEventListener('click', function () {
        if (beeInstance) {
            /* Triggers beeConfig.onSave which calls persistSave() */
            beeInstance.save();
        }
    });

    function persistSave(jsonFile, htmlFile) {
        var saveBtn = document.getElementById('save-btn');
        var statusEl = document.getElementById('save-status');
        saveBtn.disabled = true;
        statusEl.style.color = '#bdc3c7';
        statusEl.textContent = 'Saving…';

        var formData = new FormData();
        formData.append('name', document.getElementById('newsletter-name').value);
        formData.append('editor_data', jsonFile);
        formData.append('content', htmlFile);

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
    }
</script>
