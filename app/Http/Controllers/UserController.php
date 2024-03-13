<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

use App\Validators\UserValidator;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    private UserValidator $validator;
    private UserRepository $repository;

    /**
     * Creates a new instance of UserController
     */
    public function __construct()
    {
        $this->validator = new UserValidator;
        $this->repository = new UserRepository;
    }

    /**
     * Register a new user
     */
    public function signUp(Request $request)
    {
        // Validates the request
        $this->validator->verify($request->all(), $this->validator->signUpRules());

        // Get validated data
        $input = $this->validator->getData();

        // Creates the User
        $user = $this->repository->addNewUser($input);

        if( ! $user )
        {
            return $this->error( Lang::get('general.users.create_error') );
        }

        return $this->success( Lang::get('general.users.create_success') );
    }
}
