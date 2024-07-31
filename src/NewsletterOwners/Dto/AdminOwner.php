<?php

namespace Marktic\Newsletter\NewsletterOwners\Dto;

class AdminOwner implements NewsletterOwner
{
    public const TYPE = 'any';

    public int $id = 0;

    public string $type = self::TYPE;
}

