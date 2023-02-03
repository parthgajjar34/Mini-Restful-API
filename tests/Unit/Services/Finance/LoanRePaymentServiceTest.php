<?php

/**
 * LoanRePaymentServiceTest
 * PHP version 8.1
 *
 * @category Test/Unit
 * @package  tests\Unit\Services\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Unit\Services\Finance;

use App\Models\LoanApplication;
use App\Models\LoanRepaymentHistory;
use App\Models\User;
use App\Services\Finance\LoanRePaymentService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Mockery;

/**
 * Class LoanRePaymentServiceTest
 *
 * @category Tests
 * @package  tests\Unit\Services\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class LoanRePaymentServiceTest extends TestCase
{
    /**
     * @var mixed
     */
    private $service;

    /**
     * @var int
     */
    private $amountStub;

    /**
     * @var LoanApplication|Mockery\MockInterface
     */
    private $loanApplicationMock;

    /**
     * @var int
     */
    private $applicationIdStub;

    /**
     * @var int
     */
    private $interestRateStub;

    /**
     * @var Collection|Model
     */
    private $loanApplicationStub;

    /**
     * @var LoanRepaymentHistory|Mockery\MockInterface
     */
    private $loanRepaymentHistoryMock;

    /**
     * @var Mockery\Mock
     */
    private $userMock;

    /**
     * The setUp function that arranges the tests
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loanApplicationMock = Mockery::mock(LoanApplication::class)->makePartial();
        $this->app->instance(LoanApplication::class, $this->loanApplicationMock);

        $this->loanRepaymentHistoryMock = Mockery::mock(LoanRepaymentHistory::class)->makePartial();
        $this->app->instance(LoanRepaymentHistory::class, $this->loanRepaymentHistoryMock);

        $this->userMock = Mockery::mock(User::class)->makePartial();
        $this->app->instance(User::class, $this->userMock);

        $this->service = $this->app->make(LoanRePaymentService::class);
        $this->amountStub = 1000;

        $this->loanApplicationStub = LoanApplication::factory()->make(['id' => 123]);
        $this->userStub = User::factory()->make(['id' => 123]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Tests do payment doesn't have any active loan
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPaymentNonActiveLoan(): void
    {
        $this->expectException(\Exception::class);
        Auth::shouldReceive('user')->once()->andReturn($this->userStub);

        $this->loanApplicationMock->shouldReceive('getUserLoanApplicationSingleRecord')
            ->once()
            ->andReturn(null);

        $this->expectExceptionMessage(trans('messages.no_active_loan_error'));
        $this->service->doPayment($this->amountStub);
    }

    /**
     * Tests do payment has an active loan and user tries to make payment of larger amount
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPaymentActiveLoanLargePaymentAmount(): void
    {
        $this->expectException(\Exception::class);

        Auth::shouldReceive('user')->once()->andReturn($this->userStub);

        $this->loanApplicationMock->shouldReceive('getUserLoanApplicationSingleRecord')
            ->once()
            ->andReturn($this->loanApplicationStub);

        $this->loanRepaymentHistoryMock->shouldReceive('getRepaymentSingleRecordByAppId')
            ->once()
            ->andReturn($this->loanRepaymentHistoryMock);

        $this->loanRepaymentHistoryMock->shouldReceive('setAttribute', 'getAttribute')
            ->with('amount')->andReturn($this->amountStub-1);

        $this->expectExceptionMessage(
            sprintf("%s %s", trans('messages.emi_amount_error'), $this->amountStub-1)
        );
        $this->service->doPayment($this->amountStub);
    }

    /**
     * Tests do payment has an active loan but has not left any outstanding installments
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPaymentEmptyRepayment(): void
    {
        $this->expectException(\Exception::class);

        Auth::shouldReceive('user')->once()->andReturn($this->userStub);

        $this->loanApplicationMock->shouldReceive('getUserLoanApplicationSingleRecord')
            ->once()
            ->andReturn($this->loanApplicationStub);

        $this->loanRepaymentHistoryMock->shouldReceive('getRepaymentSingleRecordByAppId')
            ->once()
            ->andReturn(null);

        $this->expectExceptionMessage(trans('messages.outstanding_amount_error'));
        $this->service->doPayment($this->amountStub);
    }

    /**
     * Tests do payment has an active loan but user tries to make advance payment
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPaymentActiveFutureDate(): void
    {
        $this->expectException(\Exception::class);

        Auth::shouldReceive('user')->once()->andReturn($this->userStub);

        $this->loanApplicationMock->shouldReceive('getUserLoanApplicationSingleRecord')
            ->once()
            ->andReturn($this->loanApplicationStub);

        $this->loanRepaymentHistoryMock->shouldReceive('getRepaymentSingleRecordByAppId')
            ->once()
            ->andReturn($this->loanRepaymentHistoryMock);

        $knownDate = Carbon::parse('2022-01-01 00:00:00');
        Carbon::setTestNow($knownDate);

        $this->loanRepaymentHistoryMock->shouldReceive('setAttribute', 'getAttribute')
            ->with('amount')->andReturn($this->amountStub);

        $futureDate = $knownDate->copy()->addDay()->toDateTimeString();
        $this->loanRepaymentHistoryMock->shouldReceive('setAttribute', 'getAttribute')
            ->with('payment_date')->andReturn($futureDate);

        $this->expectExceptionMessage(
            sprintf('%s %s', trans('messages.force_future_payment_error'), $futureDate)
        );
        $this->service->doPayment($this->amountStub);
    }

    /**
     * Tests do payment has an active loan but user can make a payment
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPayment(): void
    {
        Auth::shouldReceive('user')->once()->andReturn($this->userStub);

        $this->loanApplicationMock->shouldReceive('getUserLoanApplicationSingleRecord')
            ->once()
            ->andReturn($this->loanApplicationStub);

        $this->loanRepaymentHistoryMock->shouldReceive('getRepaymentSingleRecordByAppId')
            ->once()
            ->andReturn($this->loanRepaymentHistoryMock);

        $knownDate = Carbon::parse('2022-01-01 00:00:00');
        Carbon::setTestNow($knownDate);

        $this->loanRepaymentHistoryMock->shouldReceive('setAttribute', 'getAttribute')
            ->with('amount')->andReturn($this->amountStub);

        $this->loanRepaymentHistoryMock->shouldReceive('setAttribute', 'getAttribute')
            ->with('payment_date')->andReturn($knownDate->toDateTimeString());

        $this->loanRepaymentHistoryMock->shouldReceive('save')->once();

        $this->service->doPayment($this->amountStub);
    }
}
