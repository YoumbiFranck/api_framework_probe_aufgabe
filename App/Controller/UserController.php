<?php

namespace App\Controller;

use App\Controller\BaseController;

class UserController extends BaseController
{
    public function __construct(){
        parent::__construct();
    }

    /**
     * Mit dieser Funktion kann man einen neuen User anlegen.
     * @input string 'username' → Der Benutzername des neuen Users
     * @input string 'email' → Die E-Mail-Adresse des neuen Users
     * @input string $password → Das Passwort des neuen Users
     * @return void
     */

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

    }


}