<?php

namespace Marktic\Newsletter\Providers\AbstractProvider\Traits;

trait HasApiCredentialsTrait
{
    protected string $apiKey = '';
    protected string $apiBaseUrl = '';

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiBaseUrl(string $url): static
    {
        $this->apiBaseUrl = $url;

        return $this;
    }

    public function getApiBaseUrl(): string
    {
        return $this->apiBaseUrl;
    }
}
