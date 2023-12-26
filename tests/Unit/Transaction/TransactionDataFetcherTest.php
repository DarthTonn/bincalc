<?php

namespace App\Tests\Unit\Transaction;

use App\Transaction\TransactionDataFetcher;
use App\DTO\TransactionDTO;
use App\Provider\BinProvider;
use App\Provider\CurrencyProvider;
use PHPUnit\Framework\TestCase;

class TransactionDataFetcherTest extends TestCase
{
    public function testFetchTransactionData()
    {
        $binDataProvider = $this->createMock(BinProvider::class);
        $binDataProvider->method('getBinData')->willReturn((object)['country' => (object)['alpha2' => 'US']]);

        $currencyProvider = $this->createMock(CurrencyProvider::class);
        $currencyProvider->method('getRate')->willReturn(1.2);

        $dataFetcher = new TransactionDataFetcher($binDataProvider, $currencyProvider);

        $data = [
            'bin' => '123456',
            'amount' => '100.00',
            'currency' => 'USD',
        ];

        $expectedResult = new TransactionDTO([
            'countryCode' => 'US',
            'isEu' => false,
            'rate' => 1.2,
            'amountFixed' => 8333.333333333334,
        ]);

        $result = $dataFetcher->fetchTransactionData($data);

        $this->assertInstanceOf(TransactionDTO::class, $result);
        $this->assertEquals($expectedResult->getCountryCode(), $result->getCountryCode());
        $this->assertEquals($expectedResult->isEu(), $result->isEu());
        $this->assertEquals($expectedResult->getRate(), $result->getRate());
        $this->assertEquals($expectedResult->getAmountFixed(), $result->getAmountFixed());
    }
}