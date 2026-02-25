<?php
use Marktic\Newsletter\Utility\NewsletterModels;
?>
<thead>
<tr>
    <td><?= translator()->trans('name') ?></td>
    <td><?= translator()->trans('subject') ?></td>
    <td><?= NewsletterModels::lists()->getLabel('title.singular') ?></td>
    <td><?= translator()->trans('type') ?></td>
    <td><?= translator()->trans('status') ?></td>
    <td><?= translator()->trans('created_at') ?></td>
</tr>
</thead>
