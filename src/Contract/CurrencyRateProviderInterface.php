<?php

namespace App\Contract;

interface CurrencyRateProviderInterface
{
    public function getRateToEUR(string $currency): float;
}
