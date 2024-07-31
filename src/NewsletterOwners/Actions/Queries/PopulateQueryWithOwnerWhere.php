<?php

namespace Marktic\Newsletter\NewsletterOwners\Actions\Queries;

use Marktic\Newsletter\NewsletterOwners\Dto\AnyOwner;
use Marktic\Newsletter\Utility\NewsletterUtility;

class PopulateQueryWithOwnerWhere
{
    protected $query;
    protected $owner;

    /**
     * @param $query
     * @param $owner
     */
    public function __construct($query, $owner)
    {
        $this->query = $query;
        $this->owner = $owner;
    }


    public static function for($query, $owner)
    {
        return new static($query, $owner);
    }

    public function handle()
    {
        if ($this->owner instanceof AnyOwner) {
            return $this->query;
        }
        $this->query->where('owner = ?', BillingUtility::morphLabelFor($this->owner));
        $this->query->where('owner_id = ?', $this->owner->id);
        return $this->query;
    }
}

