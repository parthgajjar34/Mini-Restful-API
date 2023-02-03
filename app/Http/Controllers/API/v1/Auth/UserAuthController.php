<?php
/**
 * Laravel Controller Class
 * PHP version 8.1
 *
 * @category App\Controllers
 * @package  App\Http\Controllers\API\v1\Finance
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */
namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UserLoginFormRequest;
use App\Http\Requests\Users\UserRegisterFormRequest;
use App\Models\User;
use App\Services\Authentication\AuthService;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * Class LoanApplicationController
 *
 * @category App\Controller
 * @package  App\Http\Controllers\API\v1\Finance
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */
class UserAuthController extends Controller
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * UserAuthController constructor
     * @param User $user
     * @param AuthService $authService
     */
    public function __construct(User $user, AuthService $authService)
    {
        $this->user = $user;
        $this->authService = $authService;
    }

    /**
     * Method for register a user
     * @param UserRegisterFormRequest $request
     * @return JsonResponse
     */
    public function register(UserRegisterFormRequest $request): JsonResponse
    {
        try {
            $data = $request->all();
            $data['password'] = bcrypt($request->password);
            $this->user->create($data);
            return $this->successMsg(trans('auth.success'));
        } catch (Exception $exception) {
            return $this->errorMsg($exception->getMessage());
        }
    }


    /**
     * Method for login a user
     * @param UserLoginFormRequest $request
     * @return JsonResponse
     */
    public function login(UserLoginFormRequest $request): JsonResponse
    {
        try {
            $token = $this->authService->authUserByEmailAndPassword(
                $request->email,
                $request->password
            );

            $emptyTokenCheck = empty($token);
            if($emptyTokenCheck) {
                return $this->errorMsg(trans('auth.failed'));
            }
            return $this->successMsg(['token' => $token]);
        } catch (Exception $exception) {
            return $this->errorMsg($exception->getMessage());
        }
    }
}
