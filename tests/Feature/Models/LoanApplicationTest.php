<?php

/**
 * LoanApplicationTest
 * PHP version 8.1
 *
 * @category Test/Feature
 * @package  Tests\Feature\Models
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Feature\Models;

use App\Enums\LoanStatusesEnum;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class LoanApplicationTest
 *
 * @category Tests
 * @package  Tests\Feature\Models
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class LoanApplicationTest extends TestCase
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

        $this->modal = $this->app->make(LoanApplication::class);
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
    public function testGetUserLoanApplicationSingleRecord(): void
    {
        $userStub = User::factory()->create();
        $loanStatusId = LoanStatusesEnum::APPROVED;
        LoanApplication::factory()->create([
            'user_id'           => $userStub->id,
            'loan_status_id'    => $loanStatusId,
        ]);

        $result = $this->modal->getUserLoanApplicationSingleRecord(
            $userStub->id,
            [$loanStatusId]
        );
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->user_id, $userStub->id);
    }

    /**
     * Tests returns empty result
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testGetUserLoanApplicationSingleRecordEmptyResult(): void
    {
        $loanStatusId = LoanStatusesEnum::APPROVED;
        $result = $this->modal->getUserLoanApplicationSingleRecord(
            123,
            [$loanStatusId]
        );
        $this->assertEmpty($result);
    }

    /**
     * Tests returns single record result
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testGetLoanApplicationByIdAndType(): void
    {
        $loanStatusId = LoanStatusesEnum::APPROVED;
        $stub = LoanApplication::factory()->create([
            'loan_status_id'    => $loanStatusId,
            'user_id'           => User::factory()->create()->id,
        ]);

        $result = $this->modal->getLoanApplicationByIdAndType(
            $stub->id,
            $loanStatusId
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
    public function testGetLoanApplicationByIdAndTypeEmptyResult(): void
    {
        $loanStatusId = LoanStatusesEnum::APPROVED;

        $result = $this->modal->getLoanApplicationByIdAndType(
            123,
            $loanStatusId
        );
        $this->assertEmpty($result);
    }
}
