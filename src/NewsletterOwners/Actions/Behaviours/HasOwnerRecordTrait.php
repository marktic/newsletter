<?php

namespace Marktic\Newsletter\NewsletterOwners\Actions\Behaviours;

use Marktic\Newsletter\NewsletterOwners\Dto\AdminOwner;
use Marktic\Newsletter\Utility\NewsletterUtility;
use Nip\Records\Record;

/**
 * @method Record getBillingOwner()
 */
trait HasOwnerRecordTrait
{
    protected Record|AdminOwner|null $owner = null;

    /**
     * @return Record|\Marktic\Billing\BillingOwner\ModelsRelated\HasOwner\HasOwnerRecordTrait
     */
    public function getOwner(): Record|AdminOwner
    {
        return $this->owner;
    }

    /**
     * @param Record|AdminOwner|null $owner
     * @return HasOwnerRecordTrait
     */
    public function withOwner(Record|AdminOwner|null $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getOwnerType()
    {
        return NewsletterUtility::morphLabelFor($this->owner);
    }

    public function getOwnerId()
    {
        return $this->owner?->id;
    }

    protected function populateRecordWithOwner($record)
    {
        $record->owner = NewsletterUtility::morphLabelFor($this->owner);
        $record->owner_id = $this->owner?->id;
    }

    protected function findParamsPopulateWithOwner($params = [])
    {
        $params['where'][] = ['owner_id = ?', $this->getOwnerId()];
        $params['where'][] = ['owner = ?', $this->getOwnerType()];

        return $params;
    }

    protected function orCreateDataNewsletterOwner($data = [])
    {
        $data['owner'] = NewsletterUtility::morphLabelFor($this->owner);
        $data['owner_id'] = $this->owner?->id;
        return $data;
    }
}
