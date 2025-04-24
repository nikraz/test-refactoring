<?php

namespace App\Tests\Provider;

use App\Provider\ExchangeRatesProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ExchangeRatesProviderTest extends TestCase
{
    public function test_it_returns_correct_rate_for_currency(): void
    {
        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->method('request')->willReturn(
            new Response(200, [], json_encode([
                'success' => true,
                'rates' => [
                    'USD' => 1.23
                ]
            ]))
        );

        $provider = new ExchangeRatesProvider($mockClient, 'https://mock-api.test', 'mock-key');
        $rate = $provider->getRateToEUR('USD');

        $this->assertSame(1.23, $rate);
    }

    public function test_it_throws_on_missing_rate(): void
    {
        $this->expectException(\RuntimeException::class);

        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->method('request')->willReturn(
            new Response(200, [], json_encode([
                'success' => true,
                'rates' => []
            ]))
        );

        $provider = new ExchangeRatesProvider($mockClient, 'https://mock-api.test', 'mock-key');
        $provider->getRateToEUR('USD');
    }
}
