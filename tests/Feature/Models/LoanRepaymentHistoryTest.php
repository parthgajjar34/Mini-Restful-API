<?php

/**
 * LoanRepaymentHistoryTest
 * PHP version 8.1
 *
 * @category Test/Feature
 * @package  Tests\Feature\Models
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Feature\Models;

use App\Enums\LoanStatusesEnum;
use App\Models\LoanApplication;
use App\Models\LoanRepaymentHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class LoanRepaymentHistoryTest
 *
 * @category Tests
 * @package  Tests\Feature\Models
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class LoanRepaymentHistoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var mixed
     */
    private $modal;

    /**
     * The setUp function that arranges the tests
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->modal = $this->app->make(LoanRepaymentHistory::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Tests returns single record result
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testGetRepaymentSingleRecordByAppId(): void
    {
        $userStub = User::factory()->create();
        $loanStatusId = LoanStatusesEnum::APPROVED;
        $loanAppStub = LoanApplication::factory()->create([
            'user_id'           => $userStub->id,
            'loan_status_id'    => $loanStatusId,
        ]);

        $stub = LoanRepaymentHistory::factory()->create([
            'loan_application_id'    => $loanAppStub->id,
            'payment_status'         => 0,
        ]);

        $result = $this->modal->getRepaymentSingleRecordByAppId(
            $loanAppStub->id
        );
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->id, $stub->id);
    }

    /**
     * Tests returns empty result
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testGetRepaymentSingleRecordByAppIdEmptyResult(): void
    {
        $result = $this->modal->getRepaymentSingleRecordByAppId(
            123
        );
        $this->assertEmpty($result);
    }
}
