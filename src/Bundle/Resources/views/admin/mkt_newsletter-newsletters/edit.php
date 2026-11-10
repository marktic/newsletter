<?php

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;
use Marktic\Newsletter\NewsletterDefinitions\DefinitionResolver;

/**
 * Editor dispatcher.
 *
 * Resolves and includes the editor view corresponding to the configured
 * driver (editor.driver in config/mkt_newsletter.php).
 *
 * Variables available to each editor partial:
 *   @var NewsletterNewsletter $item
 *   @var string               $editorDriver   e.g. 'grapesjs', 'unlayer', 'beefree'
 *   @var array                $editorOptions  driver-specific options from config
 */
$editorDriver  = $editorDriver  ?? 'grapesjs';
$editorOptions = $editorOptions ?? [];
$placeholders = $placeholders ?? DefinitionResolver::forNewsletter($item)->getPlaceholders();

/* Sanitise the driver name so it can only contain safe characters before
   using it to build a file path. */
$safeDriver = preg_replace('/[^a-z0-9_-]/', '', strtolower($editorDriver));

$partialPath = __DIR__ . '/editors/edit-' . $safeDriver . '.php';

if (!is_file($partialPath)) {
    /* Fall back to GrapesJS if the configured driver has no view file. */
    $partialPath = __DIR__ . '/editors/edit-grapesjs.php';
}

require $partialPath;
?>
<style>
    #html-source-open {
        position: fixed;
        z-index: 10000;
        top: 58px;
        right: 12px;
        padding: 7px 12px;
        border: 0;
        border-radius: 4px;
        background: #34495e;
        color: #fff;
        cursor: pointer;
    }

    /* Docked (non-modal) so the visual editor stays visible while typing. */
    #html-source-dialog[hidden] { display: none; }
    #html-source-dialog {
        position: fixed;
        z-index: 10001;
        top: 0;
        bottom: 0;
        left: 0;
        display: flex;
        width: min(640px, 50vw);
        box-shadow: 4px 0 16px rgba(0, 0, 0, .35);
        font-family: Arial, sans-serif;
    }
    #html-source-panel {
        display: flex;
        flex: 1;
        flex-direction: column;
        box-sizing: border-box;
        min-width: 0;
        padding: 16px;
        background: #fff;
    }
    #html-source-panel header,
    #html-source-panel footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    #html-source-panel form {
        display: flex;
        flex: 1;
        min-height: 0;
        flex-direction: column;
    }
    #html-source-panel textarea {
        flex: 1;
        width: 100%;
        box-sizing: border-box;
        margin: 12px 0;
        padding: 12px;
        resize: none;
        font: 13px/1.5 monospace;
    }
    #html-source-panel button {
        padding: 7px 12px;
        border: 0;
        border-radius: 4px;
        cursor: pointer;
    }
    #html-source-sync { font-size: 12px; color: #7f8c8d; }
</style>
<button type="button" id="html-source-open" aria-haspopup="dialog">Edit HTML source</button>
<div id="html-source-dialog" hidden role="dialog" aria-modal="false" aria-labelledby="html-source-title">
    <section id="html-source-panel">
        <header>
            <strong id="html-source-title">Edit complete HTML source</strong>
            <button type="button" id="html-source-close" aria-label="Close">Close</button>
        </header>
        <form method="post">
            <div class="d-flex align-items-center gap-2" aria-label="Insert personalization placeholder">
                <span>Insert:</span>
                <?php foreach ($placeholders as $token => $label): ?>
                    <button type="button" class="html-placeholder"
                            data-placeholder="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></button>
                <?php endforeach; ?>
            </div>
            <textarea name="content" aria-label="Newsletter HTML source" spellcheck="false"><?= htmlspecialchars($item->getContent() ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <footer>
                <span id="html-source-sync">Saving replaces the newsletter HTML used for previews and sending.</span>
                <button type="submit" style="background:#27ae60;color:#fff">Save HTML</button>
            </footer>
        </form>
    </section>
</div>
<script>
    (function () {
        var dialog = document.getElementById('html-source-dialog');
        var form = dialog.querySelector('form');
        var source = dialog.querySelector('textarea[name="content"]');
        var syncStatus = document.getElementById('html-source-sync');
        /* Exposed by the active editor partial (see editors/edit-*.php). */
        var bridge = window.newsletterEditorBridge || null;
        var canPushToEditor = !!(bridge && typeof bridge.setHtml === 'function');
        var pushTimer = null;
        var pullTimer = null;

        if (bridge) {
            syncStatus.textContent = canPushToEditor
                ? 'Changes are synced with the visual editor in real time.'
                : 'Visual editor changes are reflected here. This editor cannot import HTML edits.';
        }

        function pullFromEditor() {
            /* Never overwrite what the user is currently typing. */
            if (!bridge || dialog.hidden || document.activeElement === source) {
                return;
            }
            bridge.getHtml(function (html) {
                if (document.activeElement !== source && source.value !== html) {
                    source.value = html;
                }
            });
        }

        function pushToEditor() {
            clearTimeout(pushTimer);
            pushTimer = null;
            if (canPushToEditor) {
                bridge.setHtml(source.value);
            }
        }

        function schedulePush() {
            if (!canPushToEditor) {
                return;
            }
            clearTimeout(pushTimer);
            pushTimer = setTimeout(pushToEditor, 300);
        }

        if (bridge && typeof bridge.onChange === 'function') {
            bridge.onChange(function () {
                clearTimeout(pullTimer);
                pullTimer = setTimeout(pullFromEditor, 300);
            });
        }

        document.getElementById('html-source-open').addEventListener('click', function () {
            dialog.hidden = false;
            pullFromEditor();
        });
        document.getElementById('html-source-close').addEventListener('click', function () {
            if (pushTimer) {
                pushToEditor();
            }
            dialog.hidden = true;
        });

        source.addEventListener('input', schedulePush);
        source.addEventListener('blur', function () {
            if (pushTimer) {
                pushToEditor();
            }
        });

        dialog.querySelectorAll('.html-placeholder').forEach(function (button) {
            button.addEventListener('click', function () {
                var start = source.selectionStart;
                var end = source.selectionEnd;
                source.setRangeText(button.dataset.placeholder, start, end, 'end');
                source.focus();
                schedulePush();
            });
        });

        form.addEventListener('submit', function (event) {
            /* When the editor accepts HTML, save through it so the editor
               state (editor_data) and the HTML content stay consistent. */
            if (canPushToEditor && typeof bridge.save === 'function') {
                event.preventDefault();
                pushToEditor();
                bridge.save();
            }
        });
    })();
</script>
