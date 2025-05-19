<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
use Pecee\SimpleRouter\SimpleRouter;

session_start();

//set the CORS header to allow request from everywhere
//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: *');
//header('Access-Control-Allow-Headers: *');

try{
	//initialize env plugin
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
	$dotenv->load();
	try{
		$dotenv->required([
			'APP_DEBUG', 'CRYPTO_KEY',
			'DB_DRIVER', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD',
		]); //if this throws an exception, the respondError() call in the catch block won't work, since the function won't be loaded yet
	}catch(Exception $e){
		http_response_code(500);
		echo 'general error';
		die();
	}

	//initialize eloquent database support
	require_once '../boot/eloquent.php';

	//load general helper functions
	require_once '../boot/helpers.php';

	//initialize router plugin
	SimpleRouter::setDefaultNamespace('App\Controller');
	require_once '../boot/router_helpers.php';
	require_once '../boot/router_routes.php';
	SimpleRouter::start();

}catch(NotFoundHttpException $e){
	if($_ENV['APP_DEBUG'] === 'true'){
		respondError(404, 'not found', ['exception_message' => $e->getMessage()]);
	}
	respondError(404, 'not found');

}catch(Exception $e){
	if($_ENV['APP_DEBUG'] === 'true'){
		respondError(500, 'something went wrong', ['exception_file' => $e->getFile(), 'exception_line' => $e->getLine(), 'exception_message' => $e->getMessage()]);
	}else{
		respondError(500, 'something went wrong');
	}

}
