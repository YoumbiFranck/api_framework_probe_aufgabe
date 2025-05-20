<?php declare(strict_types=1);

namespace App\Middleware;

use Pecee\Http\Middleware\Exceptions\TokenMismatchException;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class AuthMiddleware implements IMiddleware{

    protected CsrfVerifier $csrfVerifier;
	public function __construct(){
        $this->csrfVerifier = new CsrfVerifier();
	}

	public function handle(Request $request): void{
		//TODO: check if user is logged in
        // CSRF prüfen, falls POST/PUT/DELETE etc.
        $this->csrfVerifier->handle($request);

//        try {
//            $this->csrf_verifier->handle($request);
//        }catch (TokenMismatchException $e){
//            //csrf token is invalid
//            response()->httpCode(401); //unauthorized
//            response()->json(['error' => 'unauthorized', 'data' => '']);
//            die();
//        }

		//if not logged in we send an error code
//		response()->httpCode(401); //unauthorized
//		response()->json(['error' => 'unauthorized', 'data' => '']);
//		die();
	}

}
