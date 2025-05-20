<?php

namespace App\Database\Repositories;

use Illuminate\Database\Capsule\Manager as DB;

class UserRepository
{

    public function __construct()
    {
        //
    }

    // This method checks if a user with the given username or email already exists in the database.
    public function userExists(string $username, string $email): bool
    {
        return DB::table('users')
            ->where('username', $username)
            ->orWhere('email', $email)
            ->exists();
    }

    public function registerUser(string $username, string $email, string $password): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        return DB::table('users')->insert([
            'username' => $username,
            'email' => $email,
            'password_hash' => $hashedPassword,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }


    public function loginUser(string $identifier, string $password): ?object
    {
        $user = DB::table('users')
            ->where('username', '=', $identifier)
            ->orWhere('email', '=', $identifier)
            ->first();

        if ($user && password_verify($password, $user->password_hash)) {
            return $user;
        }

        return null;
    }





}