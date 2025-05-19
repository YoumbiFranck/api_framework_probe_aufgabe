<?php declare(strict_types=1);

use Pecee\Http\Request;
use Pecee\Http\Response;
use Pecee\Http\Url;
use Pecee\SimpleRouter\SimpleRouter as Router;

/**
 * Get url for a route by using either name/alias, class or method name.
 *
 * The name parameter supports the following values:
 * - Route name
 * - Controller/resource name (with or without method)
 * - Controller class name
 *
 * When searching for controller/resource by name, you can use this syntax "route.name@method".
 * You can also use the same syntax when searching for a specific controller-class "MyController@home".
 * If no arguments is specified, it will return the url for the current loaded route.
 *
 * @param string|null $name
 * @param array|string|null $parameters
 * @param array|null $getParams
 * @return Url
 * @throws InvalidArgumentException
 */
function url(?string $name = null, array|string|null $parameters = null, ?array $getParams = null): Url{
	return Router::getUrl($name, $parameters, $getParams);
}

function response(): Response{
	return Router::response();
}

function request(): Request{
	return Router::request();
}

function respondSuccess(mixed $data, array $extraData = []): void{
	response()->httpCode(200);
	$payload = ['error' => '', 'data' => $data];
	if(!empty($extraData)){
		$payload = array_merge($payload, $extraData);
	}
	response()->json($payload);
	exit;
}

function respondError(int $httpCode, string $message, array $extraData = []): void{
	response()->httpCode($httpCode);
	response()->json(['error' => $message, 'data' => $extraData]);
	exit;
}
