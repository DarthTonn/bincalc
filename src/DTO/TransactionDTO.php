<?php

namespace App\DTO;

class TransactionDTO
{
    private ?string $countryCode;
    private bool $isEu;
    private float $rate;
    private float $amountFixed;

    public function __construct(array $data)
    {
        $this->countryCode = $data['countryCode'];
        $this->isEu = $data['isEu'];
        $this->rate = $data['rate'];
        $this->amountFixed = $data['amountFixed'];
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return bool
     */
    public function isEu(): bool
    {
        return $this->isEu;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @return float
     */
    public function getAmountFixed(): float
    {
        return $this->amountFixed;
    }


}