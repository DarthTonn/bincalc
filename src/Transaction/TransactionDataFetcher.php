<?php

namespace App\Transaction;

use App\DTO\TransactionDTO;
use App\Provider\BinProvider;
use App\Provider\CurrencyProvider;
use GuzzleHttp\Exception\GuzzleException;

class TransactionDataFetcher
{
    private const EU_COUNTRIES = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'];
    private const EUR = 'EUR';
    private BinProvider $binDataProvider;
    private CurrencyProvider $currencyConverter;

    /**
     * @param BinProvider $binDataProvider
     * @param CurrencyProvider $currencyConverter
     */
    public function __construct(BinProvider $binDataProvider, CurrencyProvider $currencyConverter)
    {
        $this->binDataProvider = $binDataProvider;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * @param array $data
     * @return TransactionDTO
     * @throws GuzzleException
     */
    public function fetchTransactionData(array $data): TransactionDTO
    {
        $countryCode = $this->getCountryCode($data['bin']);
        $isEu = $this->isEu($countryCode);
        $rate = $this->currencyConverter->getRate($data['currency']);
        $amountFixed = $this->calculateFixedAmount($data['amount'], $rate);

        return new TransactionDTO([
            'countryCode' => $countryCode,
            'isEu' => $isEu,
            'rate' => $rate,
            'amountFixed' => $amountFixed,
        ]);
    }

    /**
     * @param string $bin
     * @return string|null
     * @throws GuzzleException
     */
    private function getCountryCode(string $bin): ?string
    {
        try {
            $binData = $this->binDataProvider->getBinData($bin);

            return $binData->country->alpha2 ?? null;
        } catch (\Exception $e) {
            throw new \RuntimeException("Error fetching BIN data: {$e->getMessage()}");
        }
    }

    /**
     * @param string|null $countryCode
     * @return bool
     */
    private function isEu(?string $countryCode): bool
    {
        $euCountries = self::EU_COUNTRIES;

        return in_array($countryCode, $euCountries);
    }

    /**
     * @param string $amount
     * @param float $rate
     * @return float
     */
    private function calculateFixedAmount(string $amount, float $rate): float
    {
        $amountInCents = (float) ($amount * 100);

        if ($amount === self::EUR || $rate == 0) {
            return $amountInCents;
        }

        return $amountInCents / $rate;
    }
}