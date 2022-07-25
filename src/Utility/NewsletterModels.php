<?php

namespace Marktic\Newsletter\Utility;

use ByTIC\PackageBase\Utility\ModelFinder;
use Marktic\Newsletter\ConsentArtifacts\Models\NewsletterConsentArtifacts;
use Marktic\Newsletter\Consents\Models\NewsletterConsents;
use Marktic\Newsletter\ConsentStatements\Models\NewsletterConsentStatements;
use Marktic\Newsletter\Contacts\Models\NewsletterContacts;
use Marktic\Newsletter\Lists\Models\NewsletterLists;
use Marktic\Newsletter\NewsletterServiceProvider;
use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscriptions;
use Nip\Records\RecordManager;

/**
 * Class NewsletterModels
 * @package Marktic\Newsletter\Utility
 */
class NewsletterModels extends ModelFinder
{
    public const LISTS = 'lists';
    public const SUBSCRIPTIONS = 'subscriptions';
    public const CONTACTS = 'contacts';
    public const CONSENTS = 'consents';
    public const CONSENT_STATEMENTS = 'consent_statements';
    public const CONSENT_ARTIFACTS = 'consent_artifacts';

    /**
     * @return NewsletterLists|RecordManager
     */
    public static function lists()
    {
        return static::getModels(self::LISTS, NewsletterLists::class);
    }

    /**
     * @return NewsletterContacts
     */
    public static function contacts()
    {
        return static::getModels(self::CONTACTS, NewsletterContacts::class);
    }

    /**
     * @return NewsletterSubscriptions
     */
    public static function subscriptions()
    {
        return static::getModels(self::SUBSCRIPTIONS, NewsletterSubscriptions::class);
    }

    /**
     * @return NewsletterConsents|RecordManager
     */
    public static function consents()
    {
        return static::getModels(self::CONSENTS, NewsletterConsents::class);
    }

    /**
     * @return NewsletterConsentStatements
     */
    public static function consentStatements()
    {
        return static::getModels(self::CONSENT_STATEMENTS, NewsletterConsentStatements::class);
    }

    /**
     * @return NewsletterConsentStatements
     */
    public static function consentArtifacts()
    {
        return static::getModels(self::CONSENT_ARTIFACTS, NewsletterConsentArtifacts::class);
    }

    protected static function packageName(): string
    {
        return NewsletterServiceProvider::NAME;
    }
}
