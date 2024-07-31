<?php

namespace Marktic\Newsletter\Subscriptions\Models\Filters;

use Marktic\Newsletter\Lists\Actions\FindListsForOwner;
use Marktic\Newsletter\NewsletterOwners\Dto\NewsletterOwner;
use Nip\Records\Filters\AbstractFilter;
use Nip\Records\Filters\Column\Traits\HasDbNameTrait;
use Nip\Records\Locator\ModelLocator;

/**
 * Class NewsletterOwnerFilter
 * @package Galantom\Common\Models\Projects\Filters
 */
class NewsletterOwnerFilter extends AbstractFilter
{
    use HasDbNameTrait;

    protected $name = NewsletterOwner::CONTROLLER_ATTRIBUTE;

    /**
     * @inheritdoc
     */
    public function filterQuery($query)
    {
        $value = $this->getValue();
        $lists = FindListsForOwner::make()->withOwner($value)->fetch();
        $listsIds = $lists->pluck('id')->toArray();
        $query->where("{$this->getDbName()} IN ?", $listsIds);
    }

    public function cleanRequestValue($value)
    {
        return $value;
    }


    public function isValidRequestValue($value)
    {
        return is_object($value);
    }

    protected function initDbName()
    {
        $this->dbName = $this->generateDbName();
    }

    /**
     * @return string
     */
    protected function generateDbName()
    {
        $table = $this->getManager()->getRecordManager()->getTable();
        return '`' . $table . '`.`list_id`';
    }
}
