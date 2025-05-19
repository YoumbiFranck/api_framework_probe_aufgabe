<?php declare(strict_types=1);

namespace App\Controller;

use App\Database\Repositories\ExampleRepository;

class ExampleController extends BaseController{

	private ExampleRepository $example_repository;

	public function __construct(){
		parent::__construct();
		$this->example_repository = new ExampleRepository();
	}

	public function exampleGet(): void{
		$input = $this->input_handler->queryParams();

		if(empty($input['example_id'])){
			respondError(400, 'example_id is missing');
			return;
		}
		if(!is_numeric($input['example_id'])){
			respondError(400, 'example_id is invalid');
			return;
		}

		$data = $this->example_repository->get((int)$input['example_id']);

		respondSuccess($data);
	}

	public function examplePost(): void{
		$input = $this->input_handler->bodyParams();

		if(empty($input['example_parameter1'])){
			respondError(400, 'example_parameter1 is missing');
			return;
		}

		if(empty($input['example_parameter2'])){
			respondError(400, 'example_parameter2 parameter is missing');
			return;
		}else{
			if(!is_array($input['example_parameter2'])){
				respondError(400, 'example_parameter2 parameter is not an array');
				return;
			}
			foreach($input['example_parameter2'] as $numeric_value){
				if(!is_numeric($numeric_value) || $numeric_value < 1){
					respondError(400, 'example_parameter2 parameter is invalid: ' . $numeric_value);
					return;
				}
			}
		}

		$success = $this->example_repository->post($input['example_parameter1'], $input['example_parameter2']);

		respondSuccess($success);
	}

	public function examplePatch(): void{
		$input = $this->input_handler->bodyParams();

		if(empty($input['example_id'])){
			respondError(400, 'example_id is missing');
			return;
		}

		if(empty($input['example_parameter1'])){
			respondError(400, 'example_parameter1 is missing');
			return;
		}

		$affected = $this->example_repository->patch($input['example_id'], $input['example_parameter1']);

		respondSuccess($affected);
	}

	public function examplePut(): void{
		$input = $this->input_handler->bodyParams();

		if(empty($input['example_id'])){
			respondError(400, 'example_id is missing');
			return;
		}

		if(empty($input['example_parameter1'])){
			respondError(400, 'example_parameter1 is missing');
			return;
		}

		if(empty($input['example_parameter2'])){
			respondError(400, 'example_parameter2 is missing');
			return;
		}

		$affected = $this->example_repository->put($input['example_id'], $input['example_parameter1'], $input['example_parameter2']);

		respondSuccess($affected);
	}

	public function exampleDelete(): void{
		$input = $this->input_handler->bodyParams();

		if(empty($input['example_id'])){
			respondError(400, 'example_id is missing');
			return;
		}

		$affected = $this->example_repository->delete($input['example_id']);

		respondSuccess($affected);
	}

}
