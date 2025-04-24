<?php

namespace App\Service;

use App\Contract\BinProviderInterface;
use App\Contract\CurrencyRateProviderInterface;
use App\DTO\Transaction;
use App\Enum\EuCountry;

class CommissionCalculator
{
    public function __construct(
        private readonly BinProviderInterface $binProvider,
        private readonly CurrencyRateProviderInterface $rateProvider
    ) {}

    public function calculate(Transaction $transaction): float
    {
        $countryCode = $this->binProvider->getCountryCode($transaction->bin);
        $isEu = EuCountry::isEu($countryCode);

        $rate = $this->rateProvider->getRateToEUR($transaction->currency);
        $amountInEur = $transaction->amount / $rate;

        $commissionRate = $isEu ? 0.01 : 0.02;
        $commission = $amountInEur * $commissionRate;

        return $this->roundUpToCents($commission);
    }

    private function roundUpToCents(float $amount): float
    {
        return ceil($amount * 100) / 100;
    }
}
