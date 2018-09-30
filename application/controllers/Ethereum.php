<?php
use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';




class Ethereum extends REST_Controller {
	
	public $account = [];
	public $wallet = false;
	private $coinbase = '0x0';
	private function connect(){
		$web3 =  new Web3(new HttpProvider(new HttpRequestManager('http://127.0.0.1:8545', 5)));

		//$web3->personal->batch(true);
		
		return $web3;
	}
	public function index_get()
	{
		
		
		$this->set_response([
                'status' => FALSE,
                'message' => 'Api Not Support'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
	}

	public function wallet_post(){
		$password = $this->input->post("account");
		$web3 = $this->connect();

		//print_r($bitcoin->getnewaddress("Test"));

		

		$web3->personal->newAccount($password, function ($err, $account){
			
			//$this->wallet = $account;
			$arv = [
				"status" => (trim($account) ? "success" : "error"),
				"wallet" => $account
			];
			$this->response($arv);
			exit();
		});
		
		$arv = [
			"status" => "error"
		];
		$this->response($arv);

		//print_r($bitcoin->getaddressesbyaccount("Test"));
		//$bitcoin->removeaddress("33GHzp9Gx9Ftd3jP2Rpto24MUm2fw6cxhh");
	}

	


	public function walletnotify_post(){

	}

	public function blocknotify_post(){

	}

	public function validate_post(){
		$web3 = $this->connect();
		$wallet = $this->input->post("wallet");
		

		$web3->personal->listAccounts(function($err, $data) use($wallet){
			
			if(in_array($wallet, $data)){
					$arv = [
						"status" => "success",
						"wallet" => $wallet
					];
				}else{
					$arv = [
						"status" => "error"
					];
				}
				$this->response($arv);
		});
		$this->response(["status" => "error"]);
		
	}


	public function deposit_get(){
		$web3 = $this->connect();
		$wallet = [];
		$web3->personal->listAccounts(function($err, $data) use(&$wallet, $web3){
			
			foreach ($data as $key => $value) {
				$web3->eth->getBalance($value, function ($err, $balance) use(&$wallet, $web3, $value) {
					if((float)$balance->toString() > 0.001){
						$wallet[$value] = (float)$balance->toString()/1000000000000000000;
					}
					
					
				});
			}
		});

		
		$this->sendtoBase($wallet);

		

	}

	
	// Send to base coin
	private function sendtoBase($wallet){
		$server = "https://smarts.exchange/serverapi/deposit";
		$this->load->library('curl');
		$username = "admin";
		$password = "123";

	    $this->curl->create($server);
	    // Optional, delete this line if your API is open
	    $this->curl->http_login($username, $password);
	 	
	 	$arv = [];
	 	foreach ($wallet as $key => $value) {
	 		$fee = $value * 0.0001;
	 		$arv[] = [
	 			"wallet" => $key,
	 			"amount" => $value - $fee,
	 			"txt" => "",
	 			"fee" => $fee
	 		];
	 	}
	 	print_r(json_decode($arv));
	    $this->curl->post(array(
	        'data'	=>	$arv
	    ));
	    $this->curl->execute();
	}
}
