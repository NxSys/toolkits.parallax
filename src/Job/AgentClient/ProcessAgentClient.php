<?php
namespace NxSys\Toolkits\Parallax\Job\AgentClient;

class ProcessAgentClient extends BaseAgentClient
{
	public function setup()
	{
		$this->setAgentInitializationRoutine([$this, 'initProcessAgent']);
	}

	public function initProcessAgent()
	{
		// [PARALLAX_AGENT_ID] => 000148680000000054f849ab00000000648c43d8
		// [PARALLAX_JOB_LOGFILE] => C:\exec\scoop\persist\php\cli\parallax_process.log
		// [PARALLAX_JOB_LOADERPATH] => vendor/autoload.php
		// [PARALLAX_JOB_CLASS] => NxSys\Toolkits\Parallax\SampleJob
		// $this->setLogger(); #todo

		getenv('PARALLAX_AGENT_ID');
		getenv('PARALLAX_JOB_LOGFILE');
		getenv('PARALLAX_JOB_LOADERPATH');
		getenv('PARALLAX_JOB_CLASS');

		//plx+shm:000148680000000054f849ab00000000648c43d8_I?k=0xABCD&s=65280&f=w&m=700
		getenv('PARALLAX_CRN_I');
	}
}
