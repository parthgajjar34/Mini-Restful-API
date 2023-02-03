<?php

/**
 * LoanApplicationControllerTest
 * PHP version 8.1
 *
 * @category Test/Feature
 * @package  Tests\Feature\Controllers\API\v1\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Feature\Controllers\API\v1\Finance;

use App\Enums\RolesEnum;
use App\Models\LoanApplication;
use App\Models\LoanRepaymentHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;

/**
 * Class LoanApplicationControllerTest
 *
 * @category Tests
 * @package  Tests\Feature\Controllers\API\v1\Finance
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class LoanApplicationControllerTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

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
            'amount'   => 10000,
            'tenure'   => 12,
        ];
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
    public function testApplyLoanValidationFailed(): void
    {
        $user = User::factory()->create([
            'role_id' => RolesEnum::Customer,
        ]);
        $response = $this->withoutMiddleware(
            [
                CheckForMaintenanceMode::class,
            ]
        )->actingAs($user, 'api')->post("api/v1/apply-loan", $this->requestStub);

        $loanApplication = LoanApplication::where('user_id', $user->id)->first();

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true]);
        $this->assertSame($loanApplication->user_id, $user->id);
    }

    /**
     * Tests approve loan works as expected
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testApproveLoan(): void
    {
        $customer = User::factory()->create([
            'role_id' => RolesEnum::Customer,
        ]);

        $lender = User::factory()->create([
            'role_id' => RolesEnum::Customer,
        ]);

        $loanApplicationStub = LoanApplication::factory()->create([
            'user_id' => $customer->id,
        ]);
        $roiStub = 7.5;

        $response = $this->withoutMiddleware(
            [
                CheckForMaintenanceMode::class,
            ]
        )->actingAs($lender, 'api')->post("api/v1/approve-loan", [
            'application_id' => $loanApplicationStub->id,
            'interest_rate'  => $roiStub,
        ]);

        $loanRepaymentHistory = LoanRepaymentHistory::where('loan_application_id', $loanApplicationStub->id)->get();

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true]);
        $this->assertNotEmpty($loanRepaymentHistory->count());
        $this->assertSame($loanRepaymentHistory->count(), 12);
    }
}
