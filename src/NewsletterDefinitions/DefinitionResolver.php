<?php

declare(strict_types=1);

namespace Marktic\Newsletter\NewsletterDefinitions;

use Marktic\Newsletter\Base\Exceptions\NewsletterException;
use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;
use Marktic\Newsletter\Utility\PackageConfig;

class DefinitionResolver
{
    public static function forNewsletter(NewsletterNewsletter $newsletter): BaseDefinition
    {
        $class = PackageConfig::newsletterDefinitionClass($newsletter->getOwner(), $newsletter->getType());
        if (!is_a($class, BaseDefinition::class, true) || !(new \ReflectionClass($class))->isInstantiable()) {
            throw new NewsletterException(sprintf(
                'Newsletter definition [%s] must extend %s',
                $class,
                BaseDefinition::class
            ));
        }

        return new $class($newsletter);
    }
}
