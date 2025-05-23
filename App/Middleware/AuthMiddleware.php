<?php declare(strict_types=1);

namespace App\Middleware;

use Pecee\Http\Middleware\Exceptions\TokenMismatchException;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware implements IMiddleware{

    protected CsrfVerifier $csrfVerifier;
	public function __construct(){
        $this->csrfVerifier = new CsrfVerifier();
	}

	public function handle(Request $request): void{
		//TODO: check if user is logged in
        //$this->csrfVerifier->handle($request);
        $authHeader = $request->getHeader('HTTP_AUTHORIZATION') ?? $request->getHeader('Authorization');
        if(empty($authHeader)){
            respondError(401, 'Missing Authorization header');
            exit;
        }

        if(!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)){
            respondError(401, 'Invalid Authorization header');
            exit;
        }
        $jwt = $matches[1];
        try{
            $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], 'HS256'));
            $request->user = $decoded;
        }catch (\Exception $e){
            respondError(401, "Invalid token: $e");
            exit;
        }




	}

}
