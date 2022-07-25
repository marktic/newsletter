<?php

use Marktic\Newsletter\Consents\Models\NewsletterConsents;
use Marktic\Newsletter\ConsentStatements\Models\NewsletterConsentStatements;
use Marktic\Newsletter\Contacts\Models\NewsletterContacts;
use Marktic\Newsletter\Lists\Models\NewsletterLists;
use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscriptions;
use Marktic\Newsletter\Utility\NewsletterModels;

return [
    'models' => array(
        NewsletterModels::LISTS => NewsletterLists::class,
        NewsletterModels::SUBSCRIPTIONS => NewsletterSubscriptions::class,
        NewsletterModels::CONTACTS => NewsletterContacts::class,
        NewsletterModels::CONSENTS => NewsletterConsents::class,
        NewsletterModels::CONSENT_STATEMENTS => NewsletterConsentStatements::class,
    ),
    'tables' => [
        NewsletterModels::LISTS => NewsletterLists::TABLE,
        NewsletterModels::SUBSCRIPTIONS => NewsletterSubscriptions::TABLE,
        NewsletterModels::CONTACTS => NewsletterContacts::TABLE,
        NewsletterModels::CONSENTS => NewsletterConsents::TABLE,
        NewsletterModels::CONSENT_STATEMENTS => NewsletterConsentStatements::TABLE,
    ],
    'database' => [
        'connection' => 'main',
        'migrations' => true,
    ],
];
