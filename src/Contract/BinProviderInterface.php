<?php

namespace App\Contract;

interface BinProviderInterface
{
    public function getCountryCode(string $bin): string;
}