<?php
use Marktic\Newsletter\Utility\NewsletterModels;
?>
<thead>
<tr>
    <td><?= translator()->trans('first_name') ?></td>
    <td><?= translator()->trans('last_name') ?></td>
    <td><?= translator()->trans('email') ?></td>
    <td><?= NewsletterModels::lists()->getLabel('title.singular') ?></td>
    <td><?= NewsletterModels::consentStatements()->getLabel('title.singular') ?></td>
</tr>
</thead>