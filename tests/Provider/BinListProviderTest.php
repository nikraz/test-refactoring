<?php

namespace App\Tests\Provider;

use App\Provider\BinListProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class BinListProviderTest extends TestCase
{
    public function test_it_returns_country_code_from_api(): void
    {
        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->method('request')->willReturn(
            new Response(200, [], json_encode(['country' => ['alpha2' => 'DE']]))
        );

        $provider = new BinListProvider($mockClient, 'https://mock-api.test');
        $countryCode = $provider->getCountryCode('45717360');

        $this->assertSame('DE', $countryCode);
    }

    public function test_it_throws_when_country_missing(): void
    {
        $this->expectException(\RuntimeException::class);

        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->method('request')->willReturn(
            new Response(200, [], json_encode(['invalid' => 'data']))
        );

        $provider = new BinListProvider($mockClient, 'https://mock-api.test');
        $provider->getCountryCode('00000000');
    }
}
