<?php

use Marktic\Newsletter\ConsentArtifacts\Models\NewsletterConsentArtifact;
use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscription;

/** @var NewsletterSubscription $item */
$contact = $item->getNewsletterContact();

$consentArtifacts = $item->getNewsletterConsentArtifacts();
/** @var NewsletterConsentArtifact $lastArtifact */
$lastArtifact = $consentArtifacts->count() ? $consentArtifacts->end() : null;
$consentStatement = $lastArtifact?->getNewsletterConsentStatement();
?>
<tr>
    <td>
        <?= $contact?->getFirstName(); ?>
    </td>
    <td><?= $contact?->getLastName(); ?></td>
    <td><?= $contact?->getEmail(); ?></td>
    <td><?= $item->getNewsletterList()?->getName(); ?></td>
    <td><?= $consentStatement?->getText(); ?></td>
</tr>