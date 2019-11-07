<?php

include('../src/Monnify.php');


use PHPUnit\Framework\TestCase;

class MonnifyTest extends PHPUnit_Framework_TestCase
{
	private $monnify;
	private $account_number;
	private $reference;
 
    protected function setUp()
    {
        $this->monnify = new Monnify();
        $token = $this->monnify->setAccessToken();
    }

    protected function tearDown()
    {
        $this->monnify = NULL;
    }

    protected function testReserveAccount()
    {
    	$contract_code = "2957982769";
    	$this->reference = "iginla".rand();

    	$response = $this->monnify->reserveAccount($contract_code);

    	$this->account_number = $response[1];

        $this->assertEquals("alloted", $response[0]);
    }

    protected function testDeactiveAccount()
    {
    	$response = $this->monnify->deactivateAccount($this->$account_number);

        $this->assertEquals("deactivated", $response);
    }

    protected function testTransactionStatus()
    {
    	$response = $this->monnify->getTransactionStatus($this->reference);

        $this->assertEquals("reference", $response);
    }
}



