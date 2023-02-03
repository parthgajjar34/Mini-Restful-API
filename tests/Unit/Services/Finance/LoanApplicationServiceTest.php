<?php

/**
 * LoanApplicationServiceTest
 * PHP version 8.1
 *
 * @category Test/Unit
 * @package  Tests\Unit\Services\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Unit\Services\Finance;

use App\Models\LoanApplication;
use App\Models\LoanRepaymentHistory;
use App\Models\User;
use App\Services\Finance\LoanApplicationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Mockery;

/**
 * Class LoanApplicationServiceTest
 *
 * @category Tests
 * @package  Tests\Unit\Services\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class LoanApplicationServiceTest extends TestCase
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
     * @var int
     */
    private $tenureStub;

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

        $this->loanRepaymentHistoryMock = Mockery::mock(LoanRepaymentHistory::class);
        $this->app->instance(LoanRepaymentHistory::class, $this->loanRepaymentHistoryMock);

        $this->service = $this->app->make(LoanApplicationService::class);
        $this->amountStub = 1000;
        $this->tenureStub = 12;

        $this->applicationIdStub = 1;
        $this->interestRateStub = 7;

        $this->loanApplicationStub = LoanApplication::factory()->make();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Tests apply loan works as expected
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApplyLoan(): void
    {
        Auth::shouldReceive('user')->once()->andReturn(User::factory()->make(['id' => 123]));
        $this->loanApplicationMock->shouldReceive('create')
            ->once()
            ->andReturn($this->loanApplicationStub);

        $result = $this->service->applyLoan(
            $this->amountStub,
            $this->tenureStub
        );
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(LoanApplication::class, $result);
    }

    /**
     * Tests approve loan sends false when invalid loan application given
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApproveLoanEmptyLoanApplication(): void
    {
        $this->loanApplicationMock->shouldReceive('getLoanApplicationByIdAndType')
            ->once()
            ->andReturn(null);

        $result = $this->service->approveLoan(
            $this->applicationIdStub,
            $this->interestRateStub
        );
        $this->assertFalse($result);
    }

    /**
     * Tests approve works as expected
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApproveLoan(): void
    {
        $knownDate = '2022-01-01 00:00:00';
        Carbon::setTestNow($knownDate);

        $this->loanApplicationMock->shouldReceive('getLoanApplicationByIdAndType')
            ->once()
            ->andReturn($this->loanApplicationMock);

        $this->loanApplicationMock->shouldReceive('setAttribute', 'getAttribute')
            ->with('amount')
            ->andReturn($this->amountStub);

        $this->loanApplicationMock->shouldReceive('setAttribute', 'getAttribute')
            ->with('tenure')
            ->andReturn($this->tenureStub);

        $this->loanApplicationMock->shouldReceive('save')->once();

        $this->loanRepaymentHistoryMock->shouldReceive('create')->times($this->tenureStub);

        $result = $this->service->approveLoan(
            $this->applicationIdStub,
            $this->interestRateStub
        );
        $this->assertTrue($result);
    }
}
