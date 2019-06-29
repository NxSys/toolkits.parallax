<?php namespace NxSys\Toolkits\Parallax;

class ThreadBootloaderTest extends \Codeception\Test\Unit
{
	public $I;

    protected function _before()
    {
		// $this->$I=new UnitTester
		require_once __DIR__.'\..\vendor\autoload.php';
    }

    protected function _after()
    {
    }

    // tests
    public function testConstantConstants()
    {
		// $this->I->pause()
		ThreadBooter::class;
		Agent\BaseAgent::class;
		$this->assertEquals(
			ThreadBooter::DEFAULT_CHANNEL_CAPACITY,
			constant('NxSys\Toolkits\Parallax\Agent\DEFAULT_CHANNEL_CAPACITY')
		);
    }
}