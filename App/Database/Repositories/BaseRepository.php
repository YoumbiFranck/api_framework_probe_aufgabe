<?php

namespace App\Database\Repositories;

use Illuminate\Database\Query\Builder;

class BaseRepository{

	public function __construct(){
		//
	}

	public function getQueryWithBindings(Builder $query): string{
		return vsprintf(
			str_replace('?', '%s', $query->toSql()),
			collect($query->getBindings())
				->map(function($binding){
					$binding = addslashes($binding);
					return is_numeric($binding) ? $binding : "'{$binding}'";
				})
				->toArray()
		);
	}

}
