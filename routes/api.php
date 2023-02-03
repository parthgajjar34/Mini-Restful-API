<?php

use App\Http\Controllers\API\v1\Auth\UserAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\Finance\LoanApplicationController;
use App\Http\Controllers\API\v1\Finance\LoanRePaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);

    Route::group(['middleware' => ['auth:api', 'check-role-access']], function () {
        Route::post('/apply-loan', [LoanApplicationController::class, 'applyLoan']);
        Route::post('/approve-loan', [LoanApplicationController::class, 'approveLoan']);
        Route::post('/repay-loan', [LoanRePaymentController::class, 'doPayment']);
    });
});


