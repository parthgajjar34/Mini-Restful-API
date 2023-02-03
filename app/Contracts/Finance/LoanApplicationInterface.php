<?php

namespace App\Contracts\Finance;

use App\Models\LoanApplication;

interface LoanApplicationInterface
{
    /**
     * Method definition of apply loan
     * @param int $amount
     * @param int $tenure
     * @return LoanApplication
     */
    public function applyLoan(int $amount, int $tenure): LoanApplication;

    /**
     * Method definition of approve loan
     * @param int $applicationId
     * @param float $interestRate
     * @return bool
     */
    public function approveLoan(int $applicationId, float $interestRate): bool;
}
