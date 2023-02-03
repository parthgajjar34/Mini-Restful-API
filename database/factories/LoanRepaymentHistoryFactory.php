<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanRepaymentHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'loan_application_id'   =>  123,
            'amount'                =>  100,
            'payment_date'          =>  Carbon::now()->toDateTimeString(),
            'payment_status'        =>  0,
            'created_at'            =>  Carbon::now()->toDateTimeString(),
            'updated_at'            =>  Carbon::now()->toDateTimeString(),
        ];
    }
}
