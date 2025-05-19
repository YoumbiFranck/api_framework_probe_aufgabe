<?php declare(strict_types=1);

namespace App\Service;

use Pecee\Http\Input\InputHandler;
use Pecee\Http\Request;

class CustomInputHandler extends InputHandler{

	public function __construct(Request $request){
		parent::__construct($request);
	}

	/**
	 * @param array $filter only fetch items in filter
	 * @return array
	 */
	public function bodyParams(array $filter = []): array{
		// Append POST data
		$output = $_POST;

		// Append any PHP-input json
		$contents = file_get_contents('php://input');
		if(str_starts_with(trim($contents), '{')){
			$post = json_decode($contents, true);
			if($post !== false && $post !== null){
				$output += $post;
			}
		}

		return (count($filter) > 0) ? array_intersect_key($output, array_flip($filter)) : $output;
	}

	/**
	 * @param array $filter only fetch items in filter
	 * @return array
	 */
	public function queryParams(array $filter = []): array{
		return (count($filter) > 0) ? array_intersect_key($_GET, array_flip($filter)) : $_GET;
	}

}
