<?php declare(strict_types=1);

namespace App\Middleware;

use Pecee\Http\Middleware\BaseCsrfVerifier;
use Pecee\Http\Middleware\Exceptions\TokenMismatchException;
use Pecee\Http\Request;

class CsrfVerifier extends BaseCsrfVerifier{

	public function __construct(){
		parent::__construct();
		$this->tokenProvider = new CsrfCookieTokenProvider();
	}

	/**
	 * Handle request
	 *
	 * @param Request $request
	 * @throws TokenMismatchException
	 */
	public function handle(Request $request): void{
		//handle all other csrf tokens
		if($this->skip($request) === false && in_array($request->getMethod(), ['post', 'put', 'patch', 'delete'], true) === true){
			$token = $request->getHeader('http-x-csrf-token');
			if($this->tokenProvider->validate((string)$token) === false){
				throw new TokenMismatchException('Invalid CSRF-token.');
			}
		}
		$this->tokenProvider->refresh(); //refresh existing token
	}

}
