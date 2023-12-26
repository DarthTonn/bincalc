<?php

require __DIR__ . '/vendor/autoload.php'; // Autoload Composer dependencies

use App\Configuration\Configuration;
use App\Provider\CurrencyProvider;
use App\Provider\BinProvider;
use App\Transaction\TransactionProcessor;
use App\Transaction\TransactionDataFetcher;
use App\Transaction\CommissionCalculator;

// Load configuration from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Instantiate configuration
$config = [
    'exchangeRatesApiUrl' => $_ENV['EXCHANGE_RATES_API_URL'],
    'binListApiUrl' => $_ENV['BIN_LIST_API_URL'],
];

$configuration = new Configuration($config);
$client = new \GuzzleHttp\Client();

// Instantiate dependencies
$currencyConverter = new CurrencyProvider($configuration, $client);
$binProvider = new BinProvider($configuration, $client);
$fetcher = new TransactionDataFetcher($binProvider, $currencyConverter);
$calculator = new CommissionCalculator();

// Instantiate the TransactionProcessor
$transactionProcessor = new TransactionProcessor($fetcher, $calculator);

// Process transactions from input.txt
foreach ($transactionProcessor->processTransactions('input.txt') as $result) {
    echo $result . PHP_EOL;
}