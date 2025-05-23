<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Database\Repositories\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends BaseController
{
    private UserRepository $repository;
    public function __construct(){
        parent::__construct();
        $this->repository = new UserRepository();
    }


    public function createUser(): void
    {
        $input = $this->input_handler->bodyParams();

        if (empty($input['username'])) {
            respondError(400, 'username is missing');
            return;
        }

        if (empty($input['email'])) {
            respondError(400, 'email is missing');
            return;
        }

        if (empty($input['password'])) {
            respondError(400, 'password is missing');
            return;
        }
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            respondError(400, 'email is invalid');
            return;
        }

        if (strlen($input['password']) < 8) {
            respondError(400, 'password is too short');
            return;
        }

        // Prüfe auf Duplikate
        if ($this->repository->userExists($input['username'], $input['email'])) {
            respondError(409, 'Username or email already exists');
            return;
        }

        // Registrierung
        $success = $this->repository->registerUser($input['username'], $input['email'], $input['password']);

        if ($success) {
            respondSuccess(['message' => 'User successfully registered']);
        } else {
            respondError(500, 'User registration failed');
        }

    }


    public function loginUser(): void
    {
        $input = $this->input_handler->bodyParams();

        $identifier = trim($input['email_username'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($identifier)) {
            respondError(400, 'username or email is missing');
            return;
        }

        if (empty($password)) {
            respondError(400, 'password is missing');
            return;
        }

        $data = $this->repository->loginUser($identifier, $password);
        if ($data) {
            unset($data->password_hash); // Remove the password hash from the response
            $payload = [
                'user_id' => $data->id,
                'username' => $data->username,
                'email' => $data->email,
                'exp' => time() + 3600, // Token expiration time (1 hour)
            ];
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']);
            $data->token = $jwt;

            respondSuccess($data);
        } else {
            respondError(401, 'Invalid credentials');
        }
    }


    public function logoutUser(): void
    {
        respondSuccess(['message' => 'User successfully logged out. Please delete the token on the client side.']);
    }


}