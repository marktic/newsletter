<?php

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;

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

/* Sanitise the driver name so it can only contain safe characters before
   using it to build a file path. */
$safeDriver = preg_replace('/[^a-z0-9_-]/', '', strtolower($editorDriver));

$partialPath = __DIR__ . '/editors/edit-' . $safeDriver . '.php';

if (!is_file($partialPath)) {
    /* Fall back to GrapesJS if the configured driver has no view file. */
    $partialPath = __DIR__ . '/editors/edit-grapesjs.php';
}

require $partialPath;
