<?php

namespace App\Validators;

use App\Validators\BaseValidator;
use Illuminate\Validation\Rules\Password;

class UserValidator extends BaseValidator
{
    /**
     * Returns the Sign up validation rules
     * @return array
     */
    public function signUpRules(): array
    {
        return [
            'name' => 'bail|required|string|between:3,100',
            'email' => 'bail|required|email:rfc,dns,filter|unique:users',
            'password' => [
                'bail', 'required',
                Password::min(8)->letters()->mixedCase()->numbers()->uncompromised()
            ]
        ];
    }

    /**
     * Returns the Sign in validation rules
     * @return array
     */
    public function signInRules(): array
    {
        return [
            'email'    => 'bail|required|email|exists:users',
            'password' => [
                'required',
                Password::min(8)->letters()->mixedCase()->numbers()->uncompromised()
            ]
        ];
    }
}