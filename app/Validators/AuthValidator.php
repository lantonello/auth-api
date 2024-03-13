<?php

namespace App\Validators;

use App\Validators\BaseValidator;
use Illuminate\Validation\Rules\Password;

class AuthValidator extends BaseValidator
{
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