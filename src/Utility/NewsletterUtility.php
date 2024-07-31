<?php

namespace Marktic\Newsletter\Utility;

use Marktic\Billing\BillingOwner\Dto\AdminOwner;

class NewsletterUtility
{
    public static function morphLabelFor($record)
    {
        if ($record instanceof AdminOwner) {
            return $record->type;
        }
        return $record ? $record->getManager()->getMorphName() : null;
    }
}