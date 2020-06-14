<?php

namespace NxSys\Toolkits\Parallax;

use NxSys\Toolkits\Parallax\Job\BaseJob;

use const NxSys\Toolkits\Parallax\Job\JOB_STATUS_RUNNING;

/**
 * A sample job for testing
 */
class SampleJob extends BaseJob
{
	const OUTPUT="the correct output";
	public $v=1;

	public function setVal($v)
	{
		return $this->v=$v;
	}

	public function run()
	{
		//prob ProcessAgentClient
		$oAgentClient=Job\AgentClient\BaseAgentClient::getConfiguredInstance();

		//do work!
		`start cmd /c ping 127.1`;
		return $this->v;
	}
}
