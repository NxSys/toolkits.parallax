<?php namespace NxSys\Toolkits\Parallax;

class ApiConformityTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testLegacyConformity()
    {
		// ThreadBooter::class;

		//get class in legacy ::run() style
		$oJob=new SampleJob;

		//pre init
		$oJob->setVal(1);
		$oAgent=new Agent\BaseAgent;

		//execute
		$oAgent->run($oJob);
		#verify exec state

		//join
		#verify completion state/result

		$this->assertEquals(1,1);
    }
}