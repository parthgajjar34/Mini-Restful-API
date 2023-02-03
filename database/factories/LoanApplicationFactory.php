<?php

namespace Database\Factories;

use App\Enums\LoanStatusesEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'           => 123,
            'amount'            => 1000,
            'tenure'            => 12,
            'interest_rate'     => 0.00,
            'loan_status_id'    => LoanStatusesEnum::PENDING,
            'created_at'        => Carbon::now()->toDateTimeString(),
            'updated_at'        => Carbon::now()->toDateTimeString(),
        ];
    }
}
