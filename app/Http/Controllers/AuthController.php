<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

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
}
