<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class Home extends REST_Controller {
	
	

	
	public function index_get()
	{
		$this->set_response([
                'status' => FALSE,
                'message' => 'Welcome to Blockchain API'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
	}
}