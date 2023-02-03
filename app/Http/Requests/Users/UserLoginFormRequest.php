<?php

/**
 * Laravel Request Class
 * PHP version 8.1
 *
 * @category App\Requests
 * @package  Aspire mini app
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */

namespace App\Http\Requests\Users;

use App\Traits\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Class UserLoginFormRequest
 *
 * @category App\Requests
 * @package  Aspire mini app
 * @author   Parth Gajjar<parthgajjar34@gmail.com>
 */

class UserLoginFormRequest extends FormRequest
{
    use Common;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'email|required',
            'password' => 'required'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            $this->errorMsg($errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
