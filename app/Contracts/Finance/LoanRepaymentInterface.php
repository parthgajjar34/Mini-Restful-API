<?php

namespace App\Contracts\Finance;

use App\Models\LoanApplication;

interface LoanRepaymentInterface
{
    /**
     * Method definition of do payment
     * @param int $amount
     * @return void
     */
    public function doPayment(int $amount): void;
}
