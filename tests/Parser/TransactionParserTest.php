<?php

namespace App\Tests\Parser;

use App\Parser\TransactionParser;
use App\DTO\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionParserTest extends TestCase
{
    private string $tempFile;

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    private function writeToTempFile(string $contents): string
    {
        $this->tempFile = sys_get_temp_dir() . '/parser_test_' . uniqid() . '.txt';
        file_put_contents($this->tempFile, $contents);
        return $this->tempFile;
    }

    public function test_parses_multiple_valid_lines(): void
    {
        $file = $this->writeToTempFile(<<<EOL
            {"bin":"123456","amount":"100.00","currency":"EUR"}
            {"bin":"654321","amount":"200.00","currency":"USD"}
            EOL);

        $parser = new TransactionParser();
        $transactions = iterator_to_array($parser->parse($file));

        $this->assertCount(2, $transactions);
        $this->assertSame('123456', $transactions[0]->bin);
    }

    public function test_skips_empty_lines(): void
    {
        $file = $this->writeToTempFile(<<<EOL
            {"bin":"123456","amount":"100.00","currency":"EUR"}
            
            {"bin":"654321","amount":"200.00","currency":"USD"}
            EOL);

        $parser = new TransactionParser();
        $transactions = iterator_to_array($parser->parse($file));

        $this->assertCount(2, $transactions);
    }

    public function test_ignores_invalid_json_lines(): void
    {
        $file = $this->writeToTempFile(<<<EOL
            {"bin":"123456","amount":"100.00","currency":"EUR"}
            invalid-json-here
            {"bin":"654321","amount":"200.00","currency":"USD"}
            EOL);

        $parser = new TransactionParser();
        $transactions = iterator_to_array($parser->parse($file));

        $this->assertCount(2, $transactions); // Skips the invalid line
    }

    public function test_throws_if_required_fields_are_missing(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing required fields');

        $file = $this->writeToTempFile(<<<EOL
            {"bin":"123456","currency":"EUR"}
            EOL);

        $parser = new TransactionParser();
        iterator_to_array($parser->parse($file));
    }


    public function test_throws_if_amount_is_not_numeric(): void
    {
        $this->expectException(\RuntimeException::class);

        $file = $this->writeToTempFile(<<<EOL
            {"bin":"123456","amount":"notanumber","currency":"EUR"}
            EOL);

        $parser = new TransactionParser();
        iterator_to_array($parser->parse($file));
    }
}
