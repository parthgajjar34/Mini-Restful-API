<?php

/**
 * Laravel Model Class
 * PHP version 8.1
 *
 * @category App\Models
 * @package  Aspire mini app
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class LoanApplication
 *
 * @category App\Models
 * @package  Aspire mini app
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */
class LoanApplication extends BaseModel
{
    /**
     * Allow mass assignment for all except these
     * @var    array
     */
    protected $guarded = ['id'];

    /**
     * Belongs to relationship with user model
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fetch loan application by id and type
     *
     * @param int $id
     * @param int $statusId
     * @return LoanApplication|null
     */
    public function getLoanApplicationByIdAndType(int $id, int $statusId): ?LoanApplication
    {
        return $this->where('id', $id)->where('loan_status_id', $statusId)->first();
    }

    /**
     * Fetch user's loan application single record by loan status id
     * @param int $userId
     * @param array<int> $loanStatusIds
     * @return LoanApplication|null
     */
    public function getUserLoanApplicationSingleRecord(int $userId, array $loanStatusIds): ?LoanApplication
    {
        return $this->whereIn('loan_status_id', $loanStatusIds)
            ->where('user_id', $userId)->orderBy('id', 'desc')->first();
    }
}
