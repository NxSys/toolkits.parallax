<?php 

namespace NxSys\Toolkits\Parallax;

use NxSys\Toolkits\Parallax,
	NxSys\Toolkits\Parallax\Agent;

class NewProcessTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
		// $this->$I=new UnitTester
		//require_once __DIR__.'\..\vendor\autoload.php';
		$this->sSampleJobClass='NxSys\Toolkits\Parallax\SampleJob';
		$this->sPathToComposerLoader=dirname(__FILE__, 2).DIRECTORY_SEPARATOR
			.'vendor'.DIRECTORY_SEPARATOR
			.'autoload.php';
    }

    protected function _after()
    {
    }

	// tests
	public function testCanFindJobClass()
	{
		$oAgent=new Agent\ProcessAgent;
		// if($oAgent->isJobClassLocatable($this->sSampleJobClass)
		$sJobFilePath=(include $this->sPathToComposerLoader)->findFile($this->sSampleJobClass);
		codecept_debug($sJobFilePath);
		codecept_debug(realpath($sJobFilePath));
		$this->assertIsString($sJobFilePath);
		$this->assertFileExists($sJobFilePath);
	}

	public function testSampleJobAsProcess()
	{
		//pre init
		// $oJob=new SampleJob;
		// $oJob->setVal(1);
		$oAgent=new Agent\ProcessAgent;

		//setup channels
		// $oAgent->setInChannel();
		// $oAgent->setOutChannel();

		//execute
		// CST AL is.... wierd..... lets replace it
		$aParams=[
			'PARALLAX_JOB_LOADERPATH' => $this->sPathToComposerLoader,
			'val' => 1
		];
		codecept_debug($aParams);
		$oAgent->run($this->sSampleJobClass, $aParams);
		#verify exec state

		//join
		#verify completion state/result

		$this->assertEquals(1,1);
	}
}