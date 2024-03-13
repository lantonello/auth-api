<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    /**
     * Creates a new class instance
     */
    public function __construct()
    {
        $this->model = User::class;
    }

    /**
     * Adds a new User record.
     * @param array $input
     * @return User
     */
    public function addNewUser(array $input)
    {
        $data = [
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password'])
        ];

        return $this->store($data);
    }

    /**
     * Returns the fields for listing
     * @return array
     */
    protected function listFields(): array
    {
        return ['id', 'name', 'email'];
    }
}