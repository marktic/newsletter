<?php

namespace Marktic\Newsletter\Subscriptions\Models\Reports\CompleteSubscriptions;

use ByTIC\ReportGenerator\Report\AbstractReport;
use ByTIC\ReportGenerator\Report\ReportInterface;
use Marktic\Newsletter\Utility\NewsletterModels;

/**
 * Class CompleteSubscriptionsReport
 * @package Galantom\Common\Models\Donations\Reports\CompleteDonations
 */
class CompleteSubscriptionsReport extends AbstractReport implements ReportInterface
{
    /**
     * @throws \Exception
     */
    protected function define()
    {
        $fileName = NewsletterModels::subscriptions()->getLabel('title')
            . '-' . date('Y-m-d H:i:s');

        $this->getDefinition()
            ->setTitle($fileName)
            ->setFileName($fileName);

        $columns = $this->columns();
        foreach ($columns as $name => $title) {
            $this->getDefinition()
                ->addColumnSimple($name, $title);
        }
    }

    /**
     * @return array
     */
    protected function columns()
    {
        return [
            'first_name' => translator()->trans('first_name'),
            'last_name' => translator()->trans('last_name'),
            'email' => translator()->trans('email'),
            'consent_statement' => NewsletterModels::consentStatements()->getLabel('title.singular'),
            'created' => translator()->trans('created'),
        ];
    }
}
