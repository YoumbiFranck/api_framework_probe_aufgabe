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

    //---------- erstmal für den Test ----------

    //---> User registrieren
    SimpleRouter::post('/register_user', 'UserController@createUser');

    //---> User anmelden
    SimpleRouter::post('/login_user', 'UserController@loginUser');

    //----> Veranstaltung(Event) erstellen
    SimpleRouter::post('/create_event', 'EventController@createEvent');

    //----> Veranstaltung(Event) löschen
    SimpleRouter::delete('/delete_event', 'EventController@deleteEvent');

    //----> Veranstaltung(Event) aktualisieren
    SimpleRouter::put('/update_event', 'EventController@updateEvent');
    SimpleRouter::post('/update_event', 'EventController@updateEvent');

    //----> Veranstaltung(Event) abrufen
    SimpleRouter::get('/get_event', 'EventController@getEvent');

});
