<?php

namespace NxSys\Toolkits\Parallax\Job;

use NxSys\Toolkits\Parallax\Job;

use const NxSys\Toolkits\Parallax\Job\JOB_STATUS_RUNNING;

/**
 * A sample job for testing
 */
class PlainJob extends BaseJob
{

	public function setArgs(... $aArgs)
	{
		$this->aArgs=$aArgs;
	}

	public function setWork(callable $hWork)
	{
		$this->hWork=$hWork;
	}

	public function run()
	{
		$oAgentClient=Job\AgentClient\BaseAgentClient::getConfiguredInstance();
		if (!is_callable($this->hWork))
		{
			return false;
		}
		return ($this->hWork)($this->aArgs);
	}
}
