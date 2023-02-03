<?php

/**
 * Laravel Controller Class
 * PHP version 8.1
 *
 * @category App\Controllers
 * @package  App\Http\Controllers\API\v1\Finance
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */

namespace App\Http\Controllers\API\v1\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\LoanApplicationApproveFormRequest;
use App\Http\Requests\Finance\LoanApplicationFormRequest;
use App\Models\LoanApplication;
use App\Services\Finance\LoanApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Class LoanApplicationController
 *
 * @category App\Controller
 * @package  App\Http\Controllers\API\v1\Finance
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */

class LoanApplicationController extends Controller
{
    /**
     * @var LoanApplicationService
     */
    private $loanApplicationService;
    /**
     * @var LoanApplication
     */
    private $loanApplication;

    /**
     * LoanApplicationController constructor
     * @param LoanApplicationService $loanApplicationService
     * @param LoanApplication $loanApplication
     *
     * @author Parth Gajjar
     */
    public function __construct(LoanApplicationService $loanApplicationService, LoanApplication $loanApplication)
    {
        $this->loanApplicationService = $loanApplicationService;
        $this->loanApplication = $loanApplication;
    }

    /**
     * Apply a fresh loan application
     * @param LoanApplicationFormRequest $request
     * @return JsonResponse
     */
    public function applyLoan(LoanApplicationFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->loanApplicationService->applyLoan(
                $request->amount,
                $request->tenure,
            );
            DB::commit();
            return $this->successMsg('Your loan application has been submitted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorMsg($exception->getMessage());
        }
    }

    /**
     * Approve a fresh loan application
     * @param LoanApplicationApproveFormRequest $request
     * @return JsonResponse
     */
    public function approveLoan(LoanApplicationApproveFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $resultStatus = $this->loanApplicationService->approveLoan(
                $request->input('application_id'),
                $request->input('interest_rate'),
            );

            $checkFalse = !$resultStatus;
            if ($checkFalse) {
                DB::rollBack();
                return $this->errorMsg('Give loan application is invalid or already approved');
            }

            DB::commit();
            return $this->successMsg('Loan application has been approved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorMsg($exception->getMessage());
        }
    }
}
