<?php declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
	'driver' => $_ENV['DB_DRIVER'],
	'host' => $_ENV['DB_HOST'],
	'port' => $_ENV['DB_PORT'],
	'database' => $_ENV['DB_DATABASE'],
	'username' => $_ENV['DB_USERNAME'],
	'password' => $_ENV['DB_PASSWORD'],
	'charset' => 'utf8mb4',
	'collation' => 'utf8mb4_unicode_ci',
	'options' => [
		PDO::ATTR_EMULATE_PREPARES => true,
	],
]);

//make this capsule instance available globally
$capsule->setAsGlobal();

//setup the eloquent orm
$capsule->bootEloquent();
