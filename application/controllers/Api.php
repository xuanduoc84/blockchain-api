<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/Bitcoin.php';

class Api extends REST_Controller {
	
	

	private function connect(){
		return new Bitcoin($this->config->item("rpc_username"), $this->config->item("rpc_password"), $this->config->item("rpc_server"), $this->config->item("rpc_port"));
	}
	public function index_get()
	{
		$this->set_response([
                'status' => FALSE,
                'message' => 'Api Not Support'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
	}

	public function wallet_post(){
		$account = $this->input->post("account");
		$bitcoin = $this->connect();

		//print_r($bitcoin->getnewaddress("Test"));
		$wallet = $bitcoin->getnewaddress($account);
		$arv = [
			"status" => (trim($wallet) ? "success" : "error"),
			"wallet" => $wallet
		];
		$this->response($arv);
		//print_r($bitcoin->getaddressesbyaccount("Test"));
		//$bitcoin->removeaddress("33GHzp9Gx9Ftd3jP2Rpto24MUm2fw6cxhh");
	}

	public function walletnotify_post(){

	}

	public function blocknotify_post(){

	}

	public function deposit_get(){
		$bitcoin = $this->connect();
		//print_r($bitcoin);
		$data = $bitcoin->getaddressesbyaccount("SmartExchange");
		foreach ($data as $key => $value) {
			print_r($value);
		}

	}

	// Send to base coin
	private function sendtoBase(){
		$bitcoin = $this->connect();
		$bitcoin->walletpassphrase("anhkhoa@321","60");// unlock wallet
	}
}
