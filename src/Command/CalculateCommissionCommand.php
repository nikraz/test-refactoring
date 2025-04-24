<?php

namespace App\Command;

use App\Parser\TransactionParser;
use App\Provider\BinListProvider;
use App\Provider\ExchangeRatesProvider;
use App\Provider\MockBinProvider;
use App\Service\CommissionCalculator;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateCommissionCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('commission:calculate')
            ->setDescription('Calculate commission fees from a transaction file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the input file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');

        $client = new Client();

        $binProvider = $_ENV['USE_MOCK_BIN_PROVIDER'] === 'true'
            ? new MockBinProvider()
            : new BinListProvider($client, $_ENV['BIN_API_URL']);
        $rateProvider = new ExchangeRatesProvider(
            $client,
            $_ENV['EXCHANGE_API_URL'],
            $_ENV['EXCHANGE_API_KEY']
        );
        $calculator = new CommissionCalculator($binProvider, $rateProvider);
        $parser = new TransactionParser();

        foreach ($parser->parse($file) as $transaction) {
            $commission = $calculator->calculate($transaction);
            $output->writeln($commission);
        }

        return Command::SUCCESS;
    }
}
