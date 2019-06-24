<?php

namespace NxSys\Toolkits\Parallax;

use NxSys\Toolkits\Parallax;

class BasicTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
		codecept_debug("\n");
		codecept_debug(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		require_once __DIR__.'\..\vendor\autoload.php';
    }

    protected function _after()
    {
		codecept_debug(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
    }

    // tests
    public function testBaseJobCreation()
    {
		codecept_debug(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		$oPlainJob=new Parallax\Job\BaseJob();
		$this->assertInstanceOf(Parallax\Job\BaseJob::class, $oPlainJob);
    }
    
    public function testBaseAgentCreation()
    {
        codecept_debug(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		$oAgent=new Parallax\Agent\BaseAgent();
		$this->assertInstanceOf(Parallax\Agent\BaseAgent::class, $oAgent);
    }
    
    public function testBaseJobExecution()
    {
		codecept_debug(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		$sGoalValue='I am!';

		$oPlainJob=new Parallax\Job\BaseJob;
        $oAgent = new Parallax\Agent\BaseAgent;
		$hThreadFuture = $oAgent->run($oPlainJob);

		$this->assertEquals($hThreadFuture->value(), $sGoalValue);
    }
}