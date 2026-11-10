<?php

namespace Marktic\Newsletter\NewsletterFilters\Actions;

use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\AbstractModels\RecordManager;

/**
 * Deletes a filter, making sure it belongs to the given newsletter.
 */
class DeleteNewsletterFilter
{
    protected $newsletter;
    protected $filterId;
    protected ?RecordManager $repository = null;

    public static function for($newsletter, $filterId): self
    {
        $action = new static();
        $action->newsletter = $newsletter;
        $action->filterId = $filterId;
        return $action;
    }

    public function withRepository(RecordManager $repository): self
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @throws InvalidFilter
     */
    public function execute(): void
    {
        $newsletterId = (int)$this->newsletter?->getId();
        $filterId = is_numeric($this->filterId) ? (int)$this->filterId : 0;

        $filter = $filterId > 0 ? $this->getRepository()->findOne($filterId) : null;
        if (!$filter || $newsletterId < 1 || (int)$filter->getIdNewsletter() !== $newsletterId) {
            throw new InvalidFilter('Newsletter filter not found');
        }

        $filter->delete();
    }

    protected function getRepository(): RecordManager
    {
        return $this->repository ??= NewsletterModels::newsletterFilters();
    }
}
