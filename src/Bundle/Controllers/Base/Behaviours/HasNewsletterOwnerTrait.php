<?php

declare(strict_types=1);

namespace Marktic\Newsletter\Bundle\Controllers\Base\Behaviours;


trait HasNewsletterOwnerTrait
{
    protected $newsletterOwner;

    protected function getNewsletterOwner()
    {
        if ($this->newsletterOwner === null) {
            $this->setNewsletterOwner($this->generateNewsletterOwner());
        }

        return $this->newsletterOwner;
    }

    protected function checkBillingOwnerAccess($model): bool
    {
        $owner = $this->getNewsletterOwner();
        if ($model == $owner) {
            return true;
        }
        return false;
    }

    protected function generateNewsletterOwner()
    {
        $owner = $this->getOwnerFromRequest();
        if ($owner) {
            return $owner;
        }
        return $this->generateBillingOwnerDefault();
    }

    protected function getOwnerFromRequest()
    {
        $owner = $this->getRequest()->get('owner');
//        if ($owner instanceof AdminOwner) {
//            return $owner;
//        }

        return null;
    }

    protected function setNewsletterOwner($newsletterOwner)
    {
        $this->newsletterOwner = $newsletterOwner;
    }

    abstract protected function generateBillingOwnerDefault();
}