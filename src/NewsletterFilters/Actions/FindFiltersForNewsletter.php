<?php

namespace Marktic\Newsletter\NewsletterFilters\Actions;

use Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilter;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\AbstractModels\RecordManager;

class FindFiltersForNewsletter
{
    protected $newsletter;
    protected ?RecordManager $repository = null;

    public static function for($newsletter): self
    {
        $action = new static();
        $action->newsletter = $newsletter;
        return $action;
    }

    public function withRepository(RecordManager $repository): self
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return NewsletterFilter[]
     */
    public function fetch(): array
    {
        $newsletterId = $this->newsletter?->getId();
        if (empty($newsletterId)) {
            return [];
        }

        $filters = $this->getRepository()->findByParams([
            'where' => [['id_newsletter = ?', (int)$newsletterId]],
            'order' => [['id', 'ASC']],
        ]);

        $result = [];
        foreach ($filters as $filter) {
            $result[] = $filter;
        }
        return $result;
    }

    protected function getRepository(): RecordManager
    {
        return $this->repository ??= NewsletterModels::newsletterFilters();
    }
}
