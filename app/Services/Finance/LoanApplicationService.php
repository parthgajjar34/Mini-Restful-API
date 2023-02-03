<?php

/**
 * Laravel Service Class
 * PHP version 8.1
 *
 * @category App\Services
 * @package  App\Services\Finance
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */

namespace App\Services\Finance;

use App\Contracts\Finance\LoanApplicationInterface;
use App\Enums\LoanStatusesEnum;
use App\Models\LoanApplication;
use App\Models\LoanRepaymentHistory;
use App\Traits\Common;
use App\Traits\Math;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class LoanApplicationService
 *
 * @category App\Services
 * @package  App\Services\Finance
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */
class LoanApplicationService implements LoanApplicationInterface
{
    use Common, Math;

    /**
     * @var LoanApplication
     */
    private $loanApplication;
    /**
     * @var Carbon
     */
    private $carbon;
    /**
     * @var LoanRepaymentHistory
     */
    private $loanRepaymentHistory;

    /**
     * LoanApplicationService constructor
     * @param LoanApplication $loanApplication
     * @param Carbon $carbon
     * @param LoanRepaymentHistory $loanRepaymentHistory
     */
    public function __construct(LoanApplication $loanApplication, Carbon $carbon, LoanRepaymentHistory $loanRepaymentHistory)
    {
        $this->loanApplication = $loanApplication;
        $this->carbon = $carbon;
        $this->loanRepaymentHistory = $loanRepaymentHistory;
    }

    /**
     * Apply loan method
     * @param int $amount
     * @param int $tenure
     * @return LoanApplication
     */
    public function applyLoan(int $amount, int $tenure): LoanApplication
    {
        return $this->loanApplication->create([
            'user_id'           => Auth::user()->id,
            'amount'            => $amount,
            'tenure'            => $tenure,
            'loan_status_id'    => LoanStatusesEnum::PENDING,
        ]);
    }

    /**
     * Approve loan / generate repayment schedule
     * @param int $applicationId
     * @param float $interestRate
     * @return bool
     */
    public function approveLoan(int $applicationId, float $interestRate): bool
    {
        $result = $this->loanApplication->getLoanApplicationByIdAndType(
            $applicationId,
            LoanStatusesEnum::PENDING
        );

        $emptyCheck = empty($result);
        if ($emptyCheck) {
            return false;
        }

        $perWeekInterest = $this->calculateInterestByTenure(
            $result->amount,
            $interestRate,
            $result->tenure
        );

        $perWeekPrincipal = $result->amount / $result->tenure;
        $perWeekEmi = $perWeekPrincipal + $perWeekInterest;

        $weekList = $this->generateWeekDateList(abs($result->tenure));

        $loanRepaymentHistory = $this->loanRepaymentHistory;
        collect($weekList)->map(function ($weekDate) use($perWeekEmi, $loanRepaymentHistory, $applicationId) {
            $loanRepaymentHistory->create([
                'loan_application_id' =>  $applicationId,
                'amount'              =>  $perWeekEmi,
                'payment_date'        =>  $weekDate,
            ]);
        });

        $result->loan_status_id = LoanStatusesEnum::APPROVED;
        $result->interest_rate = $interestRate;
        $result->save();

        return true;
    }
}
