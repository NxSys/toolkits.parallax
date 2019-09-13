<?php

namespace NxSys\Toolkits\Parallax;

use NxSys\Toolkits\Parallax\Job\BaseJob;

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
		//do work!
		return $this->v;
	}
}
