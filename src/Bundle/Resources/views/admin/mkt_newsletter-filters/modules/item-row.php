<?php

use Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilter;

/** @var NewsletterFilter $item */
?>
<tr>
    <td><?= $item->getIdNewsletter(); ?></td>
    <td><?= $item->getType(); ?></td>
    <td><?= htmlspecialchars($item->getDescription(), ENT_QUOTES, 'UTF-8'); ?></td>
</tr>
