<?php

/**
 * LoanRePaymentControllerTest
 * PHP version 8.1
 *
 * @category Test/Feature
 * @package  Tests\Feature\Controllers\API\v1\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Feature\Controllers\API\v1\Finance;

use App\Enums\LoanStatusesEnum;
use App\Enums\RolesEnum;
use App\Models\LoanApplication;
use App\Models\LoanRepaymentHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Exception;

/**
 * Class LoanRePaymentControllerTest
 *
 * @category Tests
 * @package  Tests\Feature\Controllers\API\v1\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class LoanRePaymentControllerTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @var Collection|Model
     */
    private $userStub;

    /**
     * @var Collection|Model
     */
    private $loanApplicationStub;

    /**
     * The setUp function that arranges the tests
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->requestStub = [
            'amount'   => 1000,
        ];

        $this->userStub = User::factory()->create([
            'role_id' => RolesEnum::Customer,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Tests re-pay loan throws an error invalid loan application
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPaymentInvalidApplication(): void
    {
        $knownDate = Carbon::parse('2022-01-01 00:00:00');
        Carbon::setTestNow($knownDate);

        $this->loanApplicationStub = LoanApplication::factory()->create([
            'user_id'        => $this->userStub->id,
            'amount'         => $this->requestStub['amount'],
            'loan_status_id' => LoanStatusesEnum::COMPLETED,
        ]);

        $response = $this->withoutMiddleware(
            [
                CheckForMaintenanceMode::class,
            ]
        )->actingAs($this->userStub, 'api')->post("api/v1/repay-loan", $this->requestStub);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Tests re-pay loan throws an error when no outstanding amount left
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPaymentCompletedLoan(): void
    {
        $knownDate = Carbon::parse('2022-01-01 00:00:00');
        Carbon::setTestNow($knownDate);

        LoanApplication::factory()->create([
            'user_id'        => $this->userStub->id,
            'amount'         => $this->requestStub['amount'],
            'loan_status_id' => LoanStatusesEnum::APPROVED,
        ]);


        $response = $this->withoutMiddleware(
            [
                CheckForMaintenanceMode::class,
            ]
        )->actingAs($this->userStub, 'api')->post("api/v1/repay-loan", $this->requestStub);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false, 'error' => trans('messages.outstanding_amount_error')]);
    }

    /**
     * Tests re-pay loan throws an error when user tries to pay more amount
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPaymentAmountIsGreaterThanActualEmi(): void
    {
        $knownDate = Carbon::parse('2022-01-01 00:00:00');
        Carbon::setTestNow($knownDate);

        $loanApplicationStub = LoanApplication::factory()->create([
            'user_id'        => $this->userStub->id,
            'amount'         => $this->requestStub['amount'],
            'loan_status_id' => LoanStatusesEnum::APPROVED,
        ]);

        $stubAmount = 900;
        LoanRepaymentHistory::factory()->create([
            'loan_application_id'   =>  $loanApplicationStub->id,
            'amount'                =>  $stubAmount,
            'payment_date'          =>  $knownDate->toDateTimeString(),
        ]);


        $response = $this->withoutMiddleware(
            [
                CheckForMaintenanceMode::class,
            ]
        )->actingAs($this->userStub, 'api')->post("api/v1/repay-loan", $this->requestStub);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false, 'error' => sprintf("%s %s", trans('messages.emi_amount_error'), $stubAmount)]);
    }

    /**
     * Tests re-pay loan throws an error when user makes future date payment
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPaymentAdvancePayment(): void
    {
        $knownDate = Carbon::parse('2022-01-01 00:00:00');
        Carbon::setTestNow($knownDate);

        $loanApplicationStub = LoanApplication::factory()->create([
            'user_id'        => $this->userStub->id,
            'amount'         => $this->requestStub['amount'],
            'loan_status_id' => LoanStatusesEnum::APPROVED,
        ]);

        $amountStub = $this->requestStub['amount'];
        $paymentDateStub = $knownDate->copy()->addDay()->toDateTimeString();
        LoanRepaymentHistory::factory()->create([
            'loan_application_id'   =>  $loanApplicationStub->id,
            'amount'                =>  $amountStub,
            'payment_date'          =>  $paymentDateStub,
        ]);


        $response = $this->withoutMiddleware(
            [
                CheckForMaintenanceMode::class,
            ]
        )->actingAs($this->userStub, 'api')->post("api/v1/repay-loan", $this->requestStub);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false, 'error' => sprintf('%s %s', trans('messages.force_future_payment_error'), $paymentDateStub)]);
    }

    /**
     * Tests re-pay loan works as expected
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPayment(): void
    {
        $knownDate = Carbon::parse('2022-01-01 00:00:00');
        Carbon::setTestNow($knownDate);

        $loanApplicationStub = LoanApplication::factory()->create([
            'user_id'        => $this->userStub->id,
            'amount'         => $this->requestStub['amount'],
            'loan_status_id' => LoanStatusesEnum::APPROVED,
        ]);

        $amountStub = $this->requestStub['amount'];
        $paymentDateStub = $knownDate->toDateTimeString();
        LoanRepaymentHistory::factory()->create([
            'loan_application_id'   =>  $loanApplicationStub->id,
            'amount'                =>  $amountStub,
            'payment_date'          =>  $paymentDateStub,
        ]);


        $response = $this->withoutMiddleware(
            [
                CheckForMaintenanceMode::class,
            ]
        )->actingAs($this->userStub, 'api')->post("api/v1/repay-loan", $this->requestStub);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true]);
    }
}
