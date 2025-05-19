<?php

namespace App\Middleware;

use Pecee\Http\Security\CookieTokenProvider;

class CsrfCookieTokenProvider extends CookieTokenProvider{

	public function __construct(){
		parent::__construct();
	}

	/**
	 * Set csrf token cookie
	 *
	 * @param string $token
	 */
	public function setToken(string $token): void{
		$this->token = $token;
		setcookie(
			static::CSRF_KEY,
			$token,
			[
				'expires' => time() + (60 * $this->cookieTimeoutMinutes),
				'path' => '/',
				'domain' => ini_get('session.cookie_domain'),
				'secure' => ini_get('session.cookie_secure'),
				'httponly' => 0,
				'samesite' => 'Lax',
			]
		);
	}

}
