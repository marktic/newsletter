<?php


namespace Marktic\Newsletter\Subscriptions\Models\Reports\CompleteSubscriptions;

use ByTIC\ReportGenerator\Report\DataProvider\AbstractDataProvider;
use Generator;
use Marktic\Newsletter\ConsentArtifacts\Models\NewsletterConsentArtifact;
use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscription;
use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscriptions;
use Marktic\Newsletter\Utility\NewsletterModels;

/**
 * Class DataProvider
 * @package Marktic\Newsletter\Subscriptions\Models\Reports\CompleteSubscriptions
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @return Generator
     */
    protected function generateData()
    {
        $manager = NewsletterModels::subscriptions();
        $manager->getFilterManager()->createSession($this->getParam('filters'), 'CompleteSubscriptions');

        $query = $manager->paramsToQuery();
        $query = $manager->getFilterManager()->filterQuery($query, 'CompleteSubscriptions');

        $items = $manager->findByQuery($query);
        if (count($items)) {
            $items->loadRelations(
                NewsletterSubscriptions::RELATION_CONTACT,
                NewsletterSubscriptions::RELATION_LIST,
                NewsletterSubscriptions::RELATION_CONSENT_ARTIFACTS
            );
        }

        foreach ($items as $item) {
            yield from $this->yieldDataRow($this->constructDataRow($item));
        }
    }

    /**
     * @param NewsletterSubscription $item
     * @return array
     */
    protected function constructDataRow($item)
    {
        $contact = $item->getNewsletterContact();
        $return = [
            'first_name' => $contact?->first_name,
            'last_name' => $contact?->last_name,
            'email' => $contact?->email,
            'created' => $item->created_at,
        ];

        $return['consent_statement'] = $this->getConsentStatement($item);
        return $return;
    }

    protected function getConsentStatement(NewsletterSubscription $item)
    {
        $consentArtifacts = $item->getNewsletterConsentArtifacts();

        /** @var NewsletterConsentArtifact $lastArtifact */
        $lastArtifact = $consentArtifacts->count() ? $consentArtifacts->end() : null;
        $consentStatement = $lastArtifact?->getNewsletterConsentStatement();
        return $consentStatement?->getText();
    }
}