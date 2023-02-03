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

use App\Contracts\Finance\LoanRepaymentInterface;
use App\Enums\LoanStatusesEnum;
use App\Models\LoanApplication;
use App\Models\LoanRepaymentHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * Class LoanRePaymentService
 *
 * @category App\Services
 * @package  App\Services\Finance
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */
class LoanRePaymentService implements LoanRepaymentInterface
{
    /**
     * @var Carbon
     */
    private $carbon;
    /**
     * @var LoanApplication
     */
    private $loanApplication;
    /**
     * @var LoanRepaymentHistory
     */
    private $loanRepaymentHistory;

    /**
     * LoanRePaymentService constructor
     * @param Carbon $carbon
     * @param LoanApplication $loanApplication
     * @param LoanRepaymentHistory $loanRepaymentHistory
     */
    public function __construct(Carbon $carbon, LoanApplication $loanApplication, LoanRepaymentHistory $loanRepaymentHistory)
    {
        $this->carbon = $carbon;
        $this->loanApplication = $loanApplication;
        $this->loanRepaymentHistory = $loanRepaymentHistory;
    }

    /**
     * Do a loan repayment
     * @param int $amount
     * @return void
     * @throws Exception
     */
    public function doPayment(int $amount): void
    {
        $activeLoanApplication = $this->loanApplication->getUserLoanApplicationSingleRecord(
            Auth::user()->id,
            [
                LoanStatusesEnum::APPROVED,
                LoanStatusesEnum::IN_PROGRESS
            ],
        );

        $checkEmpty = empty($activeLoanApplication);
        if ($checkEmpty) {
            throw new Exception(trans('messages.no_active_loan_error'));
        }

        $repayment = $this->loanRepaymentHistory->getRepaymentSingleRecordByAppId($activeLoanApplication->id);

        $checkRepaymentEmpty = empty($repayment);
        if ($checkRepaymentEmpty) {
            throw new Exception(trans('messages.outstanding_amount_error'));
        }

        $checkAmount = $amount < $repayment->amount || $amount > $repayment->amount;
        if ($checkAmount) {
            throw new Exception(sprintf("%s %s", trans('messages.emi_amount_error'), $repayment->amount));
        }

        $paymentDate = $this->carbon->parse($repayment->payment_date)->timestamp;
        $now = $this->carbon->copy()->now()->startOfDay()->timestamp;

        $checkDate =  $paymentDate > $now;
        if ($checkDate) {
            throw new Exception(
                sprintf('%s %s', trans('messages.force_future_payment_error'), $repayment->payment_date)
            );
        }

        $repayment->payment_status = 1;
        $repayment->save();
    }
}
