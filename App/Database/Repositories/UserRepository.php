<?php

namespace App\Database\Repositories;

use Illuminate\Database\Capsule\Manager as DB;

class UserRepository
{

    public function __construct()
    {
        //
    }

    public function registerUser(string $username, string $email, string $password): void
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        DB::table('users')->insert([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
        ]);
    }





}