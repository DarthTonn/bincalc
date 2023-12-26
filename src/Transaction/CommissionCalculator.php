<?php

namespace App\Transaction;

class CommissionCalculator
{

    /**
     * @param float $amountFixed
     * @param bool $isEu
     * @return float
     */
    public function calculateCommission(float $amountFixed, bool $isEu): float
    {
        return $this->applyCeiling($amountFixed * ($isEu ? 0.01 : 0.02));
    }

    /**
     * @param float $commission
     * @return float
     */
    public function applyCeiling(float $commission): float
    {
        return ceil($commission) / 100;
    }
}