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

/**
 * Class LoanRepaymentHistory
 *
 * @category App\Models
 * @package  Aspire mini app
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */

class LoanRepaymentHistory extends BaseModel
{
    /**
     * Allow mass assignment for all except these
     * @var    array
     */
    protected $guarded = ['id'];


    /**
     * Fetch loan payment single record by application id
     * @param int $applicationId
     * @param int $status
     * @return LoanRepaymentHistory|null
     */
    public function getRepaymentSingleRecordByAppId(int $applicationId, int $status = 0): ?LoanRepaymentHistory
    {
        return $this->where('loan_application_id', $applicationId)
            ->where('payment_status', $status)
            ->orderBy('id', 'asc')->first();
    }
}
