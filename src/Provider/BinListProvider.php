<?php

namespace App\Provider;

use App\Contract\BinProviderInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

class BinListProvider implements BinProviderInterface
{
    public function __construct(
        private ClientInterface $client,
        private string $baseUrl
    ) {}

    public function getCountryCode(string $bin): string
    {
        try {
            $response = $this->client->request('GET', "{$this->baseUrl}/$bin");

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException("BIN API returned HTTP " . $response->getStatusCode());
            }

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['country']['alpha2'] ?? throw new \RuntimeException("Country not found for BIN $bin");

        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to fetch BIN data: " . $e->getMessage(), previous: $e);
        }
    }
}
