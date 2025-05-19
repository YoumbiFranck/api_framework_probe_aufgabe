<?php declare(strict_types=1);

namespace App\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class AuthMiddleware implements IMiddleware{

	public function __construct(){
		//
	}

	public function handle(Request $request): void{
		//TODO: check if user is logged in
		
//		//if not logged in we send an error code
//		response()->httpCode(401); //unauthorized
//		response()->json(['error' => 'unauthorized', 'data' => '']);
//		die();
	}

}
