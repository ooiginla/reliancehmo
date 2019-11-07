<?php

include('Monnify.php');

class MonnifyTest extends PHPUnit_Framework_TestCase
{
	private $monnify;
	private $contract_code;
	private $reference;
 
    protected function setUp()
    {
        $this->monnify = new Monnify();
        $this->contract_code = "2957982769";
        $this->reference = "iginla".rand();

        // connect
        $token = $this->monnify->setAccessToken();
    }

    protected function tearDown()
    {
        $this->monnify = NULL;
    }

    public function testReserveAccount()
    {
        $this->reference = "iginla".rand();
    	$response = $this->monnify->reserveAccount($this->contract_code, $this->reference);

        $this->assertEquals("allotted", $response[0]);
    }

    public function testDeactiveAccount()
    {
        $this->reference = "iginla".rand();
        $response = $this->monnify->reserveAccount($this->contract_code, $this->reference);

        $account_number = $response[1];

    	$response = $this->monnify->deactivateAccount($account_number);

        $this->assertEquals("deactivated", $response);
    }


    public function testTransactionStatus()
    {   
        $this->reference = "iginla".rand();

        $response = $this->monnify->reserveAccount($this->contract_code, $this->reference);

    	$resp2 = $this->monnify->getTransactionStatus($this->reference);

        $this->assertEquals("reference", $resp2);
    }*/
}



