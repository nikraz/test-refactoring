<?php

namespace App\Tests\Service;

use App\Contract\BinProviderInterface;
use App\Contract\CurrencyRateProviderInterface;
use App\DTO\Transaction;
use App\Service\CommissionCalculator;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    public function test_eu_transaction_returns_1_percent_commission(): void
    {
        $bin = $this->createMock(BinProviderInterface::class);
        $bin->method('getCountryCode')->willReturn('FR');

        $rate = $this->createMock(CurrencyRateProviderInterface::class);
        $rate->method('getRateToEUR')->willReturn(1.0); // EUR

        $calculator = new CommissionCalculator($bin, $rate);
        $transaction = new Transaction('400000', 100.00, 'EUR');

        $this->assertSame(1.00, $calculator->calculate($transaction));
    }

    public function test_non_eu_transaction_with_conversion_returns_2_percent_commission(): void
    {
        $bin = $this->createMock(BinProviderInterface::class);
        $bin->method('getCountryCode')->willReturn('US');

        $rate = $this->createMock(CurrencyRateProviderInterface::class);
        $rate->method('getRateToEUR')->willReturn(2.0); // USD

        $calculator = new CommissionCalculator($bin, $rate);
        $transaction = new Transaction('123456', 100.00, 'USD');

        $this->assertSame(1.00, $calculator->calculate($transaction)); // (100 / 2) * 0.02 = 1.00
    }
}
