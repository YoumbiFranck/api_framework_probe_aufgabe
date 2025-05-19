<?php declare(strict_types=1);

namespace App\Database\Repositories;

use Illuminate\Database\Capsule\Manager as DB;

class ExampleRepository{

	public function __construct(){
		//
	}

	public function get(int $example_id): object|null{
		return DB::table('example_table')
			->select([
				'example_id',
				'example_field1',
				'example_field2'
			])
			->where('example_id', '=', $example_id)
			->first();
	}

	public function post(string $example_parameter1, array $example_parameter2): bool{
		$inserts = [];
		foreach($example_parameter2 as $numeric_value){
			$inserts[] = [
				'example_string_field' => $example_parameter1,
				'example_numeric_field' => $numeric_value,
			];
		}
		return DB::table('example_table')
			->insert($inserts);
	}

	public function patch(int $example_id, string $example_parameter1): int{
		return DB::table('example_table')
			->where('example_id', '=', $example_id)
			->update(['example_field1' => $example_parameter1]);
	}

	public function put(int $example_id, string $example_parameter1, string $example_parameter2): int{
		return DB::table('example_table')
			->where('example_id', '=', $example_id)
			->update([
				'example_field1' => $example_parameter1,
				'example_field2' => $example_parameter2,
			]);
	}

	public function delete(int $example_id): int{
		return DB::table('example_table')
			->where('example_id', '=', $example_id)
			->delete();
	}

}
