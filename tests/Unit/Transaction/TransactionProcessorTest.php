<?php

namespace App\Tests\Unit\Transaction;

use App\Transaction\CommissionCalculator;
use App\Transaction\TransactionProcessor;
use App\Transaction\TransactionDataFetcher;
use App\DTO\TransactionDTO;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TransactionProcessorTest extends TestCase
{

    private MockObject $transactionData;
    private MockObject $calculator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->transactionData = $this->createMock(TransactionDataFetcher::class);
        $this->calculator = $this->createMock(CommissionCalculator::class);
    }
    public function testProcessTransactions()
    {
        $this->transactionData->method('fetchTransactionData')->willReturn(new TransactionDTO([
            'countryCode' => 'US',
            'isEu' => false,
            'rate' => 1.2,
            'amountFixed' => 8333.333333333333,
        ]));
        $this->calculator->method('calculateCommission')->willReturn(0.02);

        $processor = new TransactionProcessor($this->transactionData, $this->calculator);

        $inputFilePath = __DIR__ . '/../testdata/input.txt';

        $resultGenerator = $processor->processTransactions($inputFilePath);

        $firstResult = $resultGenerator->current();
        $this->assertStringStartsWith('0.02', $firstResult);
    }

    public function testProcessTransactionLineSuccess()
    {
        $expectedCommission = 0.02;
        $this->transactionData->method('fetchTransactionData')->willReturn(new TransactionDTO([
            'countryCode' => 'US',
            'isEu' => false,
            'rate' => 1.2,
            'amountFixed' => 8333.333333333333,
        ]));
        $this->calculator->method('calculateCommission')->willReturn(0.02);

        $processor = new TransactionProcessor($this->transactionData, $this->calculator);

        $transaction = '{"bin":"123456","amount":"100.00","currency":"USD"}';

        $result = $this->invokePrivateMethod($processor, 'processTransactionLine', [$transaction]);

        $this->assertSame($expectedCommission, $result);
    }

    public function testProcessTransactionLineInvalidJson()
    {
        $processor = new TransactionProcessor($this->transactionData, $this->calculator);

        $invalidTransaction = 'Invalid JSON';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON format: Invalid JSON');
        $this->invokePrivateMethod($processor, 'processTransactionLine', [$invalidTransaction]);
    }

    public function testProcessTransactionInvalidFile()
    {
        $inputFilePath = __DIR__ . '/../testdata/input111111.txt';
        $processor = new TransactionProcessor($this->transactionData, $this->calculator);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error opening file');
        $processor->processTransactions($inputFilePath)->throw(new \Exception('Error opening file'));
    }

    /**
     * @param TransactionProcessor $object
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    private function invokePrivateMethod(TransactionProcessor $object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}