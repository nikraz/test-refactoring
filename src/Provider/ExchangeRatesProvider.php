<?php

namespace App\Provider;

use App\Contract\CurrencyRateProviderInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class ExchangeRatesProvider implements CurrencyRateProviderInterface
{
    public function __construct(
        private ClientInterface $client,
        private string $url,
        private string $apiKey
    ) {}

    public function getRateToEUR(string $currency): float
    {
        if ($currency === 'EUR') {
            return 1.0;
        }

        $response = $this->client->request('GET', $this->url, [
            'headers' => [
                'apikey' => $this->apiKey,
                'Accept' => 'application/json'
            ],
            'query' => [
                'base' => 'EUR',
                'symbols' => $currency
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (!($data['success'] ?? false)) {
            $errorMsg = $data['error']['info'] ?? 'Unknown exchange API error';
            throw new \RuntimeException("Exchange API error: $errorMsg");
        }

        return $data['rates'][$currency] ?? throw new \RuntimeException("Missing exchange rate for: $currency");
    }
}
