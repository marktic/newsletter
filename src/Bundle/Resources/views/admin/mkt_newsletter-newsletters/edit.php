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

    #html-source-dialog[hidden] { display: none; }
    #html-source-dialog {
        position: fixed;
        z-index: 10001;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(0, 0, 0, .6);
        font-family: Arial, sans-serif;
    }
    #html-source-panel {
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        width: min(1100px, 100%);
        height: min(800px, 100%);
        padding: 16px;
        border-radius: 6px;
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
</style>
<button type="button" id="html-source-open" aria-haspopup="dialog">Edit HTML source</button>
<div id="html-source-dialog" hidden role="dialog" aria-modal="true" aria-labelledby="html-source-title">
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
                <span>Saving replaces the newsletter HTML used for previews and sending.</span>
                <button type="submit" style="background:#27ae60;color:#fff">Save HTML</button>
            </footer>
        </form>
    </section>
</div>
<script>
    (function () {
        var dialog = document.getElementById('html-source-dialog');
        document.getElementById('html-source-open').addEventListener('click', function () {
            dialog.hidden = false;
        });
        document.getElementById('html-source-close').addEventListener('click', function () {
            dialog.hidden = true;
        });
        var source = dialog.querySelector('textarea[name="content"]');
        dialog.querySelectorAll('.html-placeholder').forEach(function (button) {
            button.addEventListener('click', function () {
                var start = source.selectionStart;
                var end = source.selectionEnd;
                source.setRangeText(button.dataset.placeholder, start, end, 'end');
                source.focus();
            });
        });
        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) {
                dialog.hidden = true;
            }
        });
    })();
</script>
