<?php

namespace Marktic\Newsletter\NewsletterOwners\Dto;

class AnyOwner implements NewsletterOwner
{
    public const TYPE = 'admin';

    public int $id = 0;

    public string $type = self::TYPE;
}

