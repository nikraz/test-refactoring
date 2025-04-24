<?php

namespace App\Provider;

use App\Contract\BinProviderInterface;

class MockBinProvider implements BinProviderInterface
{
    private array $binToCountry = [
        '45717360' => 'DK',
        '516793'   => 'US',
        '45417360' => 'JP',
        '41417360' => 'US',
        '4745030'  => 'GB',
        '400000'   => 'FR',
    ];

    public function getCountryCode(string $bin): string
    {
        foreach ($this->binToCountry as $prefix => $countryCode) {
            if (str_starts_with($bin, $prefix)) {
                return $countryCode;
            }
        }

        return 'US';
    }
}
