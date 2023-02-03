<?php

/**
 * Laravel Controller Class
 * PHP version 8.1
 *
 * @category App\Controller
 * @package  App\Http\Controllers\API\v1\Finance
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */
namespace App\Http\Controllers\API\v1\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\LoanRePaymentFormRequest;
use App\Services\Finance\LoanRePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Class LoanRePaymentController
 *
 * @category App\Controllers
 * @package  App\Http\Controllers\API\v1\Finance
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */
class LoanRePaymentController extends Controller
{
    /**
     * @var LoanRePaymentService
     */
    private $loanRePaymentService;

    /**
     * LoanRePaymentController constructor
     *
     * @param LoanRePaymentService $loanRePaymentService
     * @author Parth Gajjar<parthgajjar34@gmail.com>
     */
    public function __construct(LoanRePaymentService $loanRePaymentService)
    {
        $this->loanRePaymentService = $loanRePaymentService;
    }

    /**
     * Make a loan repayment
     * @param LoanRePaymentFormRequest $request
     * @return JsonResponse
     */
    public function doPayment(LoanRePaymentFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->loanRePaymentService->doPayment($request->amount);
            $this->successMsg('Loan repayment has been done successfully.');
            DB::commit();
            return $this->successMsg(trans('messages.payment_success'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorMsg($exception->getMessage());
        }
    }
}
