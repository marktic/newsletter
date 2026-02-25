<?php

use Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilter;

/** @var NewsletterFilter $item */
?>
<tr>
    <td><?= $item->getIdNewsletter(); ?></td>
    <td><?= $item->getType(); ?></td>
    <td><?= json_encode($item->getValues()); ?></td>
</tr>
