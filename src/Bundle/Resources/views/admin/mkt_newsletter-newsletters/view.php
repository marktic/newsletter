<?php

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;

/**
 * Newsletter details page: inline preview on the left, filters and
 * preview email cards in the right column.
 *
 * @var NewsletterNewsletter                                           $item
 * @var \Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilter[] $filters
 * @var \Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypeInterface[] $filterTypes
 * @var array                                                          $urls preview|addFilter|deleteFilter|sendPreview
 */
$filters = $filters ?? [];
$filterTypes = $filterTypes ?? [];
$urls = $urls ?? [];
$escape = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$trans = static fn(string $key): string => function_exists('translator')
    ? (string)translator()->trans('mkt_newsletter-newsletters.' . $key)
    : $key;
$content = $item->getContent();
$hasContent = $content !== null && trim($content) !== '';
?>
<div class="row mkt-newsletter-view">
    <div class="col-lg-8 mb-4">
        <?php require __DIR__ . '/modules/view/preview-card.php'; ?>
    </div>
    <div class="col-lg-4">
        <?php require __DIR__ . '/modules/view/filters-cards.php'; ?>
        <?php require __DIR__ . '/modules/view/send-preview-card.php'; ?>
    </div>
</div>
