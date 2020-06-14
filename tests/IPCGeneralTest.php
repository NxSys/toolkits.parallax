<?php

namespace NxSys\Toolkits\Parallax;

use NxSys\Toolkits\Parallax,
	NxSys\Toolkits\Parallax\Agent;


use NxSys\Toolkits\Parallax\Channel as IpcChannel;
use NxSys\Toolkits\Parallax\Channel\InMemoryChannel;

class IPCGeneralTest extends \Codeception\Test\Unit
{
    protected function _before()
    {

    }

    protected function _after()
    {
    }

	// tests

	public function testFullCRNIdempotence()
	{
		$oMemChannel=new IpcChannel\InMemoryChannel;
		$sCRN='urn:nxsys-ipcrn:shm:000148680000000054f849ab00000000648c43d8_I?Key=819121034219898082&SegmentFlag=c&SegmentMode=700&SegmentSize=65280';
		$oMemChannel->openChannelResourceName($sCRN);
		$sCurrentCRN=$oMemChannel->getChannelResourceName();
		$this->assertEquals($sCRN, $sCurrentCRN);
		return;
	}
	public function testPartialCRNIdempotence()
	{
		$oMemChannel=new IpcChannel\InMemoryChannel();
		$oMemChannel->setId(rand());
		$oMemChannel->open();
		$sMyCRN1=$oMemChannel->getChannelResourceName();
		$oMemChannel2=new IpcChannel\InMemoryChannel();
		$oMemChannel2->openChannelResourceName($sMyCRN1);
		$sMyCRN2=$oMemChannel2->getChannelResourceName();
		$this->assertEquals($sMyCRN1, $sMyCRN2);
		// $this->assertEquals($oMemChannel, $oMemChannel2); //not quite...
	}

	//testSizes
	//testFlagsAndOptions
	//testLeakage?

	public function testUniverse()
	{
		$this->assertEquals(1,1);
	}
}