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
		$oPlainJob=new class extends Parallax\Job\BaseJob
		{
			public function run()
			{
				$sGoal="I am!";
				echo $sGoal;
			}
		};
		$this->assertInstanceOf(Parallax\Job\BaseJob::class, $oPlainJob);
    }
    public function testBaseJobExecution()
    {
		codecept_debug(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		$sGoalValue='I am!';

		$oPlainJob=new Parallax\Job\BaseJob;
		$hThreadFuture = $oPlainJob->start();
		$sJobReturnValue=$oPlainJob->resolveJobToValue();

		$this->assertEquals($sGoalValue, $sJobReturnValue);
		$this->assertEquals($hThreadFuture->value(), $sJobReturnValue);
    }
}