<?php
namespace NxSys\Toolkits\Parallax;


use NxSys\Toolkits\Parallax\Job\BaseJob;

/**
 * A sample job for testing
 */
class SampleJob extends BaseJob
{
	const OUTPUT="the correct output";
	public function run()
	{
		return self::OUTPUT;
	}
}
