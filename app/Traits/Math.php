<?php

/**
 * Laravel Trait
 * PHP version 8.1
 *
 * @category App\Traits
 * @package  App\Traitsp
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */

namespace App\Traits;

/**
 * Trait Common
 *
 * @category App\Traits
 * @package  App\Traits
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */

trait Math
{
    /**
     * Calculate simple rate of interest by tenure
     * @param int $amount
     * @param float $rateOfInterest
     * @param int $tenure
     * @return float
     */
    public function calculateInterestByTenure(int $amount, float $rateOfInterest, int $tenure): float
    {
        return (($amount * $rateOfInterest) / 100) / $tenure;
    }
}
