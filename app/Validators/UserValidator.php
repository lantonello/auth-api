<?php

namespace App\Validators;

use App\Validators\BaseValidator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserValidator extends BaseValidator
{
    /**
     * Returns the Sign up validation rules
     * @return array
     */
    public function signUpRules(): array
    {
        return [
            'name'     => 'bail|required|string|between:3,100',
            'email'    => 'bail|required|email:rfc,dns,filter|unique:users',
            'password' => [
                'bail', 'required',
                Password::min(8)->letters()->mixedCase()->numbers()->uncompromised()
            ]
        ];
    }

    /**
     * Returns the update record validation rules
     * @return array
     */
    public function updateRules( int $user_id ): array
    {
        return [
            'name'  => 'bail|required|string|between:3,100',
            'email' => [
                'bail', 'required', 'email:rfc,dns,filter',
                Rule::unique('users')->ignore( $user_id )
            ],
        ];
    }
}