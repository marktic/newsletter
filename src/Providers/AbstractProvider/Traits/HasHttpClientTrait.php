<?php

namespace Marktic\Newsletter\Providers\AbstractProvider\Traits;

trait HasHttpClientTrait
{
    protected array $defaultHeaders = [];

    protected function buildHeaders(): array
    {
        return $this->defaultHeaders;
    }

    protected function get(string $url, array $query = []): array
    {
        $fullUrl = $this->getApiBaseUrl() . $url;
        if (!empty($query)) {
            $fullUrl .= '?' . http_build_query($query);
        }

        $ch = curl_init($fullUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->formatHeaders($this->buildHeaders()),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    protected function post(string $url, array $data = []): array
    {
        $fullUrl = $this->getApiBaseUrl() . $url;

        $ch = curl_init($fullUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $this->formatHeaders(array_merge(
                $this->buildHeaders(),
                ['Content-Type: application/json']
            )),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    protected function put(string $url, array $data = []): array
    {
        $fullUrl = $this->getApiBaseUrl() . $url;

        $ch = curl_init($fullUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $this->formatHeaders(array_merge(
                $this->buildHeaders(),
                ['Content-Type: application/json']
            )),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    protected function patch(string $url, array $data = []): array
    {
        $fullUrl = $this->getApiBaseUrl() . $url;

        $ch = curl_init($fullUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $this->formatHeaders(array_merge(
                $this->buildHeaders(),
                ['Content-Type: application/json']
            )),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    protected function delete(string $url): bool
    {
        $fullUrl = $this->getApiBaseUrl() . $url;

        $ch = curl_init($fullUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => $this->formatHeaders($this->buildHeaders()),
        ]);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_exec($ch);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }

    private function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            if (is_int($key)) {
                $formatted[] = $value;
            } else {
                $formatted[] = "$key: $value";
            }
        }

        return $formatted;
    }
}
