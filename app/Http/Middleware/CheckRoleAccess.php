<?php
/**
 * Laravel Middleware Class
 * PHP version 8.1
 *
 * @category App\Middleware
 * @package  Aspire mini app
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */
namespace App\Http\Middleware;

use App\Enums\ActionLabelEnum;
use App\Enums\RolesEnum;
use App\Traits\Common;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Class CheckRoleAccess
 *
 * @category App\Middleware
 * @package  Aspire mini app
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */

class CheckRoleAccess
{
    use Common;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $action = $this->getActionName(Route::getCurrentRoute()->getActionName());

        $allowAction = false;
        switch (Auth::user()->role_id) {
            case RolesEnum::Customer:
                $allowAction = $action === ActionLabelEnum::APPLY_LOAN || $action === ActionLabelEnum::DO_PAYMENT;
                break;
            case RolesEnum::Lender:
                $allowAction = $action === ActionLabelEnum::APPROVE_LOAN;
                break;
        }

        if (!$allowAction) {
            return $this->errorMsg("You're not allowed to perform this action");
        }

        return $next($request);
    }
}
