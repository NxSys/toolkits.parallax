<?php

namespace NxSys\Toolkits\Parallax;

use NxSys\Toolkits\Parallax,
	NxSys\Toolkits\Parallax\Agent;


use NxSys\Toolkits\Parallax\Channel as IpcChannel;
use NxSys\Toolkits\Parallax\Channel\InMemoryChannel;

class IPCInMemTest extends \Codeception\Test\Unit
{
    protected function _before()
    {

    }

    protected function _after()
    {
    }

	// tests
	public function testCanOpenChannel()
	{
		$oMemChannel=new IpcChannel\InMemoryChannel;
		$this->assertInstanceOf('NxSys\Toolkits\Parallax\Channel\InMemoryChannel', $oMemChannel);

		$sCRN='urn:nxsys-ipcrn:shm:000148680000000054f849ab00000000648c43d8_I?SegmentSize=65280&SegmentFlag=c&SegmentMode=700&Key=ABCD';
		$iErrorLevel=$oMemChannel->openChannelResourceName($sCRN);
		$this->assertEquals(0, $iErrorLevel); //So we have an assertion.
	}

	/**
	 * Undocumented function
	 *
	 * @depends testCanOpenChannel
	 *
	 * @return void
	 */
	public function testCanSendMessage()
	{
		$oMemChannel=new IpcChannel\InMemoryChannel;
		$this->assertInstanceOf('NxSys\Toolkits\Parallax\Channel\InMemoryChannel', $oMemChannel);
		$sCRN='urn:nxsys-ipcrn:shm:000148680000000054f849ab00000000648c43d8_I?SegmentSize=65280&SegmentFlag=c&SegmentMode=700&Key=ABCD';
		$iErrorLevel=$oMemChannel->openChannelResourceName($sCRN);
		$this->assertEquals(0, $iErrorLevel); //So we have an assertion.
		$oMsg=new Channel\Message;
		$iMsgLen=strlen(serialize($oMsg));
		$ret=$oMemChannel->enqueue($oMsg);
		$this->assertNotNull($ret, 'Message has failed to enqueue');
		$this->assertEquals($iMsgLen, $ret, 'Size of Message posted differs!');
	}

	/**
	 * @depends testCanSendMessage
	 */
	public function testCanReceiveMessage()
	{
		$oMemChannel=new IpcChannel\InMemoryChannel;
		$sCRN='urn:nxsys-ipcrn:shm:000148680000000054f849ab00000000648c43d8_I?SegmentSize=65280&SegmentFlag=c&SegmentMode=700&Key=ABCD';
		$oMemChannel->openChannelResourceName($sCRN);
		$oMyMsg=new Channel\Message;
		$iMsgLen=strlen(serialize($oMyMsg));
		$ret=$oMemChannel->enqueue($oMyMsg);
		$this->assertNotNull($ret, 'Message has failed to enqueue');
		$oMsg=$oMemChannel->dequeue();
		$this->assertEquals($iMsgLen, strlen(serialize($oMsg)));
	}

	public function testReceveValidMessageContent()
	{
		$oMemChannel=new IpcChannel\InMemoryChannel;
		$sCRN='urn:nxsys-ipcrn:shm:000148680000000054f849ab00000000648c43d8_I?SegmentSize=65280&SegmentFlag=c&SegmentMode=700&Key=ABCD';
		$oMemChannel->openChannelResourceName($sCRN);
		$oMyMsg=new Channel\Message;
		$sTestValue='ATESTVALUE';
		$sTestLabel='ATESTLABEL';
		$oMyMsg->sLabel=$sTestLabel;
		$oMyMsg->mValue=$sTestValue;
		$iMsgLen=strlen(serialize($oMyMsg));
		$ret=$oMemChannel->enqueue($oMyMsg);
		$this->assertNotNull($ret, 'Message has failed to enqueue');
		$oMsg=$oMemChannel->dequeue();
		$this->assertEquals($iMsgLen, strlen(serialize($oMsg)));
		$this->assertEquals($sTestLabel, $oMsg->sLabel);
		$this->assertEquals($sTestValue, $oMsg->mValue);
		$this->assertEquals($oMyMsg, $oMsg);
	}

	public function testReusableChannel()
	{
		$oMemChannel=new IpcChannel\InMemoryChannel;
		$sCRN='urn:nxsys-ipcrn:shm:000148680000000054f849ab00000000648c43d8_I?SegmentSize=65280&SegmentFlag=c&SegmentMode=700&Key=ABCD';
		$oMemChannel->openChannelResourceName($sCRN);
		$oMyMsg=new Channel\Message();
		$sTestValue='ATESTVALUE';
		$sTestLabel='ATESTLABEL';
		$sTestValue2='AVALUBLEValue';
		$sTestLabel2='ALABORIOUSLabel';


		$oMyMsg->sLabel=$sTestLabel;
		$oMyMsg->mValue=$sTestValue;
		$ret=$oMemChannel->enqueue($oMyMsg);
		$this->assertNotNull($ret, 'Message has failed to enqueue');
		$oMsg=$oMemChannel->dequeue();
		$this->assertEquals($oMyMsg, $oMsg);

		$oANewMsg=new Channel\Message();
		$oANewMsg->sLabel=$sTestLabel2;
		$oANewMsg->mValue=$sTestValue2;
		$ret=$oMemChannel->enqueue($oANewMsg);
		$this->assertNotNull($ret, 'Message has failed to enqueue');
		$oMsg=$oMemChannel->dequeue();
		$this->assertEquals($oANewMsg, $oMsg);

	}


	//testSizes
	//testFlagsAndOptions
	//testLeakage?

	public function testUniverse()
	{
		$this->assertEquals(1,1);
	}
}