<?php

namespace App\Controller;

use App\Service\CustomInputHandler;

class BaseController{

	protected CustomInputHandler $input_handler;

	public function __construct(){
		$this->input_handler = new CustomInputHandler(request());
	}

}
