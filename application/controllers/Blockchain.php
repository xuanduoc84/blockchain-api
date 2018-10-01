<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/Bitcoin.php';

class Blockchain extends REST_Controller {
	
	

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
		$wallet = $bitcoin->getnewaddress("SmartExchange");
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

	public function validate_post(){
		$bitcoin = $this->connect();
		
		$wallet = $this->input->post("wallet");
		$data = $bitcoin->getaddressesbyaccount("SmartExchange");
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
	}
	public function deposit_get(){
		$bitcoin = $this->connect();
		
		$data = $bitcoin->getaddressesbyaccount("SmartExchange");
		
		$arv = [];
		
		
		if(is_array($data)){

			foreach ($data as $key => $value) {
				$getreceivedbyaddress = $bitcoin->getreceivedbyaddress($value);
					$arv = [
						"symbol" => "BTC",
						"address" => $value,
						"getreceivedbyaddress" => $getreceivedbyaddress,
						"updated" => date("Y-m-d :i:s")
					];
					$read = $this->db->get_where("deposit",["symbol" => "BTC", "address" => $value])->row();
					if(!$read){
						$this->db->insert("deposit",$arv);
					}else{
						$this->db->update("deposit",$arv,["id" => $read->id]);
					}

				//print_r($bitcoin->sendmany($value,"3DfWhRHdTKGehXpYHNXooSLUmBqmUHnbFe"));
				//sendfrom($address,$address2,$amount)
			}
		}
		
		$data = $this->db->get_where("deposit",["symbol" => "BTC"])->result();
		$arv = [];
		$server = "https://smarts.exchange/deposit.html";
		foreach ($data as $key => $value) {
			if($value->getreceivedbyaddress > $value->senddeposit){
				
				$number = $value->getreceivedbyaddress - $value->senddeposit;
				$arv[] = [
			 			"wallet" => $value->address,
			 			"amount" => number_format($number,4),
			 			"txt" => $this->uuid("210.211.121.20"),
			 			"fee" => 0,
			 			"symbol"	=>	"BTC"
			 		];

			 	$this->db->update("deposit",["senddeposit" => $value->getreceivedbyaddress],["id" => $value->id]);
			}
		}

		$ch = curl_init( $server );
		# Setup request to send json via POST.
		$headers = array(
		    'Content-Type:application/json',
		    'Authorization: Basic '. base64_encode("admin:anhkhoa123") // <---
		);

		$payload = json_encode( $arv  );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt(CURLOPT_HTTPAUTH, constant('CURLAUTH_BASIC'));
		//curl_setopt(CURLOPT_USERPWD, 'admin:anhkhoa123');

		# Return response instead of printing.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		# Send request.
		$result = curl_exec($ch);
		curl_close($ch);
		# Print response.
		print_r($payload);


	}

	public function uuid($serverID=1)
		{
		    $t=explode(" ",microtime());
		    return sprintf( '%04x-%08s-%08s-%04s-%04x%04x',
		        $serverID,
		        $this->clientIPToHex(),
		        substr("00000000".dechex($t[1]),-8),   // get 8HEX of unixtime
		        substr("0000".dechex(round($t[0]*65536)),-4), // get 4HEX of microtime
		        mt_rand(0,0xffff), mt_rand(0,0xffff));
		}

	public	function clientIPToHex($ip="") {
		    $hex="";
		    if($ip=="") $ip=getEnv("REMOTE_ADDR");
		    $part=explode('.', $ip);
		    for ($i=0; $i<=count($part)-1; $i++) {
		        $hex.=substr("0".dechex($part[$i]),-2);
		    }
		    return $hex;
		}

	public	function clientIPFromHex($hex) {
		    $ip="";
		    if(strlen($hex)==8) {
		        $ip.=hexdec(substr($hex,0,2)).".";
		        $ip.=hexdec(substr($hex,2,2)).".";
		        $ip.=hexdec(substr($hex,4,2)).".";
		        $ip.=hexdec(substr($hex,6,2));
		    }
		    return $ip;
		}

	// Send to base coin
	private function sendtoBase(){
		//$bitcoin = $this->connect();
		//print_r($bitcoin->getbalance("3LXS6roX2KZTQcrQhYmeerwQ63tjZSuEkb",6));// unlock wallet
	}
}
