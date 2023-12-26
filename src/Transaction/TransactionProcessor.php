<?php

namespace App\Transaction;

use GuzzleHttp\Exception\GuzzleException;

class TransactionProcessor
{
    private TransactionDataFetcher $dataFetcher;
    private CommissionCalculator $calculator;

    /**
     * @param TransactionDataFetcher $dataFetcher
     * @param CommissionCalculator $calculator
     */
    public function __construct(TransactionDataFetcher $dataFetcher, CommissionCalculator $calculator)
    {
        $this->dataFetcher = $dataFetcher;
        $this->calculator = $calculator;
    }

    /**
     * @param string $inputFilePath
     * @return \Generator
     * @throws GuzzleException
     * @throws \Exception
     */
    public function processTransactions(string $inputFilePath): \Generator
    {
        $fileHandle = fopen($inputFilePath, 'r');

        if (!$fileHandle) {
            throw new \Exception('Error opening file');
        }

        try {
            while ($line = fgets($fileHandle)) {
                if (trim($line) !== '') {
                    yield $this->processTransactionLine($line);
                }
            }
        } finally {
            fclose($fileHandle);
        }
    }

    /**
     * @param string $transaction
     * @return float
     * @throws GuzzleException
     * @throws \RuntimeException
     */
    private function processTransactionLine(string $transaction): float
    {
            $data = json_decode($transaction, true);

            if (!$data) {
                throw new \InvalidArgumentException("Invalid JSON format: $transaction");
            }

        try {
            $transactionData = $this->dataFetcher->fetchTransactionData($data);

            return $this->calculator->calculateCommission($transactionData->getAmountFixed(), $transactionData->isEu());
        } catch (\Exception $e) {
            throw new \RuntimeException('Error processing transaction: ' . $e->getMessage());
        }
    }
}