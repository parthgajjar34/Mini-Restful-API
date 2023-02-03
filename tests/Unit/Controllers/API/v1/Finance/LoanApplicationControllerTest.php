<?php

/**
 * LoanApplicationControllerTest
 * PHP version 8.1
 *
 * @category Test/Unit
 * @package  Tests\Unit\Controllers\API\v1\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Unit\Controllers\API\v1\Finance;

use App\Models\User;
use App\Services\Finance\LoanApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Mockery;
use Exception;

/**
 * Class LoanApplicationControllerTest
 *
 * @category Tests
 * @package  Tests\Unit\Controllers\API\v1\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class LoanApplicationControllerTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @var array
     */
    private $requestStub;

    /**
     * @var LoanApplicationService|Mockery\MockInterface
     */
    private $loanApplicationServiceMock;

    /**
     * The setUp function that arranges the tests
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loanApplicationServiceMock = Mockery::mock(LoanApplicationService::class);
        $this->app->instance(LoanApplicationService::class, $this->loanApplicationServiceMock);

        $this->requestStub = [
            'amount'   => 10000,
            'tenure'   => 12,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Tests apply loan validation errors
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApplyLoanValidationFailed(): void
    {
        $this->loanApplicationServiceMock->shouldReceive('applyLoan')->times(0);
        DB::shouldReceive('beginTransaction')->times(0);
        DB::shouldReceive('commit')->times(0);
        DB::shouldReceive('rollback')->times(0);
        $response = $this->mockMiddleware()->post("api/v1/apply-loan", []);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Tests apply loan work as expected
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApplyLoan(): void
    {
        $this->loanApplicationServiceMock->shouldReceive('applyLoan')->once();
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollback')->times(0);
        $response = $this->mockMiddleware()->post("api/v1/apply-loan", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true]);
    }

    /**
     * Tests apply loan throws an exception
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApplyLoanException(): void
    {
        $this->loanApplicationServiceMock->shouldReceive('applyLoan')
            ->once()
            ->andThrow(Exception::class);
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->times(0);
        DB::shouldReceive('rollback')->once();
        $response = $this->mockMiddleware()->post("api/v1/apply-loan", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Tests approve loan sends validation errors
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApproveLoanValidationFailed(): void
    {
        $this->loanApplicationServiceMock->shouldReceive('approveLoan')->times(0);
        DB::shouldReceive('beginTransaction')->times(0);
        DB::shouldReceive('commit')->times(0);
        DB::shouldReceive('rollback')->times(0);
        $response = $this->mockMiddleware()->post("api/v1/approve-loan", []);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Tests approve loan work with invalid application
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApproveLoanInvalidApplication(): void
    {
        $this->loanApplicationServiceMock->shouldReceive('approveLoan')
            ->once()
            ->andReturn(false);
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->times(0);
        DB::shouldReceive('rollback')->once();

        $this->requestStub['application_id'] = 123;
        $this->requestStub['interest_rate'] = 10;

        $response = $this->mockMiddleware()->post("api/v1/approve-loan", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Tests approve loan works as expected
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApproveLoanSuccess(): void
    {
        $this->loanApplicationServiceMock->shouldReceive('approveLoan')
            ->once()
            ->andReturn(true);
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollback')->times(0);

        $this->requestStub['application_id'] = 123;
        $this->requestStub['interest_rate'] = 10;

        $response = $this->mockMiddleware()->post("api/v1/approve-loan", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true]);
    }

    /**
     * Tests approve loan throws an exception
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApproveLoanThrowAnException(): void
    {
        $this->loanApplicationServiceMock->shouldReceive('approveLoan')
            ->once()
            ->andThrows(Exception::class);
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->times(0);
        DB::shouldReceive('rollback')->once();

        $this->requestStub['application_id'] = 123;
        $this->requestStub['interest_rate'] = 10;

        $response = $this->mockMiddleware()->post("api/v1/approve-loan", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false]);
    }


    /**
     * Mock Middleware
     * @return LoanApplicationControllerTest
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     */
    private function mockMiddleware(): LoanApplicationControllerTest
    {
        $user = User::factory()->make(['id' => 1]);
        return $this->withoutMiddleware(
            [
                CheckForMaintenanceMode::class,
            ]
        )
            ->actingAs($user, 'api');
    }
}
