<?php

use Marktic\Newsletter\NewsletterItems\Models\NewsletterItem;

/** @var NewsletterItem $item */
?>
<tr>
    <td><?= $item->getIdNewsletter(); ?></td>
    <td><?= $item->getRecordId(); ?></td>
    <td><?= $item->getRecordType(); ?></td>
    <td><?= $item->getStatus(); ?></td>
</tr>
