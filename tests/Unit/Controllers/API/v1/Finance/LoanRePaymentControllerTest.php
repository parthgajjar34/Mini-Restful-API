<?php

/**
 * LoanRePaymentControllerTest
 * PHP version 8.1
 *
 * @category Test/Unit
 * @package  Tests\Unit\Controllers\API\v1\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Unit\Controllers\API\v1\Finance;

use App\Models\User;
use App\Services\Finance\LoanApplicationService;
use App\Services\Finance\LoanRePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Mockery;
use Exception;

/**
 * Class LoanRePaymentControllerTest
 *
 * @category Tests
 * @package  Tests\Unit\Controllers\API\v1\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class LoanRePaymentControllerTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @var array
     */
    private $requestStub;

    /**
     * @var LoanRePaymentService|Mockery\MockInterface
     */
    private $loanRePaymentServiceMock;

    /**
     * The setUp function that arranges the tests
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loanRePaymentServiceMock = Mockery::mock(LoanRePaymentService::class);
        $this->app->instance(LoanRePaymentService::class, $this->loanRePaymentServiceMock);

        $this->requestStub = [
            'amount'   => 100,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Tests re-pay loan validation errors
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPaymentValidationFailed(): void
    {
        $this->loanRePaymentServiceMock->shouldReceive('doPayment')->times(0);
        DB::shouldReceive('beginTransaction')->times(0);
        DB::shouldReceive('commit')->times(0);
        DB::shouldReceive('rollback')->times(0);
        $response = $this->mockMiddleware()->post("api/v1/repay-loan", []);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Tests re-pay loan works as expected
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPayment(): void
    {
        $this->loanRePaymentServiceMock->shouldReceive('doPayment')->once();
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollback')->times(0);
        $response = $this->mockMiddleware()->post("api/v1/repay-loan", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true]);
    }

    /**
     * Tests re-pay loan throws an exception
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testDoPaymentThrowsAnException(): void
    {
        $this->loanRePaymentServiceMock->shouldReceive('doPayment')->once()
            ->andThrow(Exception::class);
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->times(0);
        DB::shouldReceive('rollback')->once();
        $response = $this->mockMiddleware()->post("api/v1/repay-loan", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Mock Middleware
     * @return LoanRePaymentControllerTest
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     */
    private function mockMiddleware(): LoanRePaymentControllerTest
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
