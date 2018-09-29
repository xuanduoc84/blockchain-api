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
		$web3 =  new Web3(new HttpProvider(new HttpRequestManager('http://127.0.0.1:8545', 0.5)));

		//$web3->personal->batch(true);
		$web3->personal->listAccounts(function($err, $data){
			$this->account = $data;
		});
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
				"wallet" => "0x".$account
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

	public function wallet_get(){
		$password = "ConBo";
		$web3 = $this->connect();

		//print_r($bitcoin->getnewaddress("Test"));
		$web3->personal->newAccount($password, function ($err, $account){
			
			//$this->wallet = $account;
			$arv = [
				"status" => (trim($account) ? "success" : "error"),
				"wallet" => "0x".$account
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
		$this->connect();
		$wallet = $this->input->post("wallet");
		
		if(in_array($wallet, $this->account)){
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
	}


	public function deposit_get(){
		$web3 = $this->connect();
		
		foreach ($this->account as $key => $value) {
			$web3->eth->getBalance($value, function ($err, $balance) {
				if((float)$balance->toString() > 0.001){
					echo 'Balance: ' . $balance->toString() . PHP_EOL;
				}
				
			});
		}

	}

	
	// Send to base coin
	private function sendtoBase(){
		$bitcoin = $this->connect();
		print_r($bitcoin->getbalance("3LXS6roX2KZTQcrQhYmeerwQ63tjZSuEkb",6));// unlock wallet
	}
}
