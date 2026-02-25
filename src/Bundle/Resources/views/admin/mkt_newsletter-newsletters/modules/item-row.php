<?php

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;
use Marktic\Newsletter\Utility\NewsletterModels;

/** @var NewsletterNewsletter $item */
?>
<tr>
    <td><?= $item->getName(); ?></td>
    <td><?= $item->getSubject(); ?></td>
    <td><?= $item->getNewsletterList()?->getName(); ?></td>
    <td><?= $item->getType(); ?></td>
    <td><?= $item->getStatus(); ?></td>
    <td><?= $item->getCreatedAt(); ?></td>
</tr>
