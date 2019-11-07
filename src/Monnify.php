<?php

Class Monnify{

	private $token;

	function __construct(){
		// initialize here;
	}

	public function setAccessToken()
	{
		if(empty($this->token))
		{
			$respObj = json_decode($this->initialize());

			if($respObj->requestSuccessful &&
			   $respObj->responseMessage == "success" &&
			   $respObj->responseCode == "0"
			 ){
				$this->token = $respObj->responseBody->accessToken;
			}
		}

		return $this->token;
	}

	public function initialize()
	{
		$url = 'https://sandbox.monnify.com/api/v1/auth/login';
		$data = [];

		// Initializes a new cURL session
		$curl = curl_init($url);

		// Set the CURLOPT_RETURNTRANSFER option to true
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Set the CURLOPT_POST option to true for POST request
		curl_setopt($curl, CURLOPT_POST, true);

		// Set the request data as JSON using json_encode function
		curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));

		// Set custom headers for RapidAPI Auth and Content-Type header
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
		  'Authorization: Basic '.
		  base64_encode('MK_TEST_WD7TZCMQV7:H5EQMQSHSURJNQ7UH2R78YAH6UN54ZP7')
		]);

		// Execute cURL request with all previous settings
		$response = curl_exec($curl);

		// Close cURL session
		curl_close($curl);

		return $response;
	}

	public function callAPI($method, $url, $data)
	{
	   $curl = curl_init();

	   switch ($method){
	      case "POST":
	         curl_setopt($curl, CURLOPT_POST, 1);
	         if ($data)
	            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	         break;
	      case "PUT":
	         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
	         if ($data)
	            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
	         break;

	      case "DEL":
	         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
	         if ($data)
	            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
	         break;
	      default:
	         if ($data)
	            $url = sprintf("%s?%s", $url, http_build_query($data));
	   }

	   // OPTIONS:
	   curl_setopt($curl, CURLOPT_URL, $url);

	   if($method=="POST"){
	   		 curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			      'Authorization: Bearer '. $this->token,
			      'Content-Type: application/json',
			  ));
	   }else{
	   		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			      'Authorization: Basic '. $this->token,
			      'Content-Type: application/json',
			  ));
	   }
	  
	   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	   // EXECUTE:
	   $result = curl_exec($curl);
	   if(!$result){die("Connection Failure");}
	   curl_close($curl);

	   return $result;
	}

	public function reserveAccount($contract_code, $reference)
	{
		$url = "https://sandbox.monnify.com/api/v1/bank-transfer/reserved-accounts";

		$data = [
			"accountReference" => $reference,
		    "accountName" => "Test Reserved Account",
		    "currencyCode" => "NGN",
		    "contractCode" => $contract_code,
		    "customerEmail" => "iginla.omotayo@gmail.com"
		];

		$response = $this->callAPI('POST', $url, json_encode($data));
		$respObj = json_decode($response);

		if($respObj->requestSuccessful && $respObj->responseMessage == "success"){
			return ["allotted", $respObj->responseBody->accountNumber];
		}
	}

	public function deactivateAccount($accountNumber)
	{
		$url = "https://sandbox.monnify.com/api/v1/bank-transfer/reserved-accounts/".	
				$accountNumber;

		$response = $this->callAPI('GET', $url, false);
		$respObj = json_decode($response);

		return "deactivated";

		if($respObj->requestSuccessful && $respObj->responseMessage == "success"){
			return "deactivated";
		}
	}

	public function getTransactionStatus($reference){
		$url = "https://sandbox.monnify.com/api/v1/merchant/transactions/query";
		$url .= '?paymentReference='.$reference;

		$response = $this->callAPI('GET', $url, false);
		$respObj = json_decode($response);

		var_dump($respObj);
		if($respObj->requestSuccessful && $respObj->responseMessage == "success"){
			return "reference";
		}
	}
}


$monnify = new Monnify();

$monnify->setAccessToken();

$ref = 'iginla001'.rand();

$respo = $monnify->reserveAccount('2957982769', $ref);

$resp = $monnify->getTransactionStatus($ref);

//var_dump($resp);
/*
object(stdClass)#4 (4) { ["requestSuccessful"]=> bool(true) ["responseMessage"]=> string(7) "success" ["responseCode"]=> string(1) "0" ["responseBody"]=> object(stdClass)#5 (12) { ["contractCode"]=> string(10) "2957982769" ["accountReference"]=> string(6) "iginla" ["accountName"]=> string(21) "Test Reserved Account" ["currencyCode"]=> string(3) "NGN" ["customerEmail"]=> string(24) "iginla.omotayo@gmail.com" ["accountNumber"]=> string(10) "1561821883" ["bankName"]=> string(13) "Providus Bank" ["bankCode"]=> string(3) "101" ["reservationReference"]=> string(20) "5ATMQXJL3JHX8ZNPUATG" ["status"]=> string(6) "ACTIVE" ["createdOn"]=> string(23) "2019-11-07 14:57:42.331" ["incomeSplitConfig"]=> array(0) { } } } */