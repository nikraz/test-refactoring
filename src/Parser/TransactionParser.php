<?php

namespace App\Parser;

use App\DTO\Transaction;

class TransactionParser
{
    public function parse(string $filePath): iterable
    {
        foreach (file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $data = json_decode($line, true);

            if (!is_array($data)) continue;

            if (!isset($data['bin'], $data['amount'], $data['currency'])) {
                throw new \RuntimeException("Missing required fields in line: $line");
            }

            if (!is_numeric($data['amount'])) {
                throw new \RuntimeException("Amount must be numeric in line: $line");
            }

            yield new Transaction(
                bin: $data['bin'],
                amount: (float) $data['amount'],
                currency: $data['currency']
            );
        }
    }
}
