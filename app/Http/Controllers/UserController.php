<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;

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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->boolean('with_deleted') )
        {
            return $this->repository->listAll();
        }

        return $this->repository->list();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->signUp( $request );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->repository->get( $id );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        // Get update validation rules
        $rules = $this->validator->updateRules( $id );

        $this->validator->verify($request->all(), $rules);

        // Updates the User
        $user = $this->repository->store( $this->validator->getData(), $id );

        if( ! $user )
        {
            return $this->error( Lang::get('general.users.update_error') );
        }

        return $this->success( Lang::get('general.users.update_success') );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $result = $this->repository->delete( $id );

        if( ! $result )
        {
            return $this->error( Lang::get('general.users.delete_error') );
        }

        return $this->success( Lang::get('general.users.delete_success') );
    }

    /**
     * Register a new user
     */
    public function signUp(Request $request)
    {
        // Validates the request
        $this->validator->verify($request->all(), $this->validator->signUpRules());

        // Creates the User
        $user = $this->repository->addNewUser( $this->validator->getData() );

        if( ! $user )
        {
            return $this->error( Lang::get('general.users.create_error') );
        }

        return $this->success( Lang::get('general.users.create_success') );
    }
}
