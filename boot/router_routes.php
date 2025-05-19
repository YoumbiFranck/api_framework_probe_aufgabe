<?php declare(strict_types=1);

use App\Middleware\AuthMiddleware;
use Pecee\SimpleRouter\SimpleRouter;

//SimpleRouter::csrfVerifier(new CsrfVerifier()); //enable csrf verification

//GET = fetch an object
//POST = create an object
//PATCH = change a single value of an object
//PUT = change all values of an object
//DELETE = delete an object

SimpleRouter::group(['prefix' => '/api'], function(){

	//---------- public routes ----------
	SimpleRouter::get('/example_get', 'ExampleController@exampleGet');

	//---------- auth routes ----------
	SimpleRouter::group(['middleware' => AuthMiddleware::class], function(){
		SimpleRouter::post('/example_post', 'ExampleController@examplePost');
		SimpleRouter::patch('/example_patch', 'ExampleController@examplePatch');
		SimpleRouter::put('/example_put', 'ExampleController@examplePut');
		SimpleRouter::delete('/example_delete', 'ExampleController@exampleDelete');
	});

});
