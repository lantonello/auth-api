<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use App\Validators\AuthValidator;
use App\Models\User;
use App\Helpers\JsonWebToken;

class AuthController extends Controller
{
    private AuthValidator $validator;

    /**
     * Creates a new instance of AuthController
     */
    public function __construct()
    {
        $this->validator = new AuthValidator;
    }

    /**
     * User authentication
     */
    public function signIn(Request $request)
    {
        // Validate credentials
        $this->validator->verify( $request->only(['email', 'password']), $this->validator->signInRules() );

        $credentials = $this->validator->getData();

        if( ! Auth::attempt($credentials) )
        {
            return $this->error( Lang::get('auth.failed') );
        }

        // Load user, generate token and return
        $user = Auth::user();
        $jwt  = JsonWebToken::generate( $user );
        $data = ['name' => $user->name, 'token' => $jwt];

        return $this->success( Lang::get('auth.success', ['name' => $user->name]), $data );
    }

    /**
     * Validates and sends reset password link
     */
    public function forgotPassword(Request $request)
    {
        // Validate
        $this->validator->verify( $request->only('email'), $this->validator->forgotPasswordRules() );

        $status = Password::sendResetLink( $request->only('email') );

        if( $status != Password::RESET_LINK_SENT )
        {
            return $this->error( Lang::get($status) );
        }

        return $this->success( Lang::get('general.reset_link_sent') );
    }

    /**
     * Reset user's password
     */
    public function resetPassword(Request $request)
    {
        // Validate
        $this->validator->verify( $request->all(), $this->validator->resetPasswordRules() );

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
            }
        );

        if( $status != Password::PASSWORD_RESET )
        {
            return $this->error( Lang::get($status) );
        }

        return $this->success( Lang::get('general.password_reset') );
    }
}
