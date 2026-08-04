<?php

namespace Marktic\Newsletter\Providers\AbstractProvider;

use Marktic\Newsletter\Providers\AbstractProvider\Contracts\ProviderInterface;
use Marktic\Newsletter\Providers\AbstractProvider\Traits\HasApiCredentialsTrait;
use Marktic\Newsletter\Providers\AbstractProvider\Traits\HasHttpClientTrait;

abstract class AbstractProvider implements ProviderInterface
{
    use HasApiCredentialsTrait;
    use HasHttpClientTrait;

    abstract public function getName(): string;

    public static function create(string $apiKey): static
    {
        $provider = new static();
        $provider->setApiKey($apiKey);

        return $provider;
    }
}
