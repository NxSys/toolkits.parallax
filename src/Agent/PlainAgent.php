<?php
/**
 * $BaseName$
 * $Id$
 *
 * DESCRIPTION
 *
 *
 * @link https://onx.zulipchat.com
 *
 * @package Parallax
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2019 Nexus Systems, inc.
 *
 * @author William Graber <wgraber@nxs.systems>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

/** @namespace Native Namespace */
namespace NxSys\Toolkits\Parallax\Agent;

/** Local Project Dependencies **/
use NxSys\Toolkits\Parallax;
use NxSys\Toolkits\Parallax\Job;
use NxSys\Toolkits\Parallax\Job\BaseJob;
use NxSys\Toolkits\Parallax\Job\Fiber;

/** Framework Dependencies **/
use parallel\Runtime as Thread_Runtime;
use parallel\Channel as Thread_Channel;

//....
use RuntimeException;
use ReflectionClass;

/** Library Dependencies **/
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;

use NxSys\Toolkits\Parallax\Channel;

use const NxSys\Toolkits\Parallax\Job\JOB_STATUS_CLOSED;
use const NxSys\Toolkits\Parallax\Job\JOB_STATUS_RUNNING;

/**
 *
 */
class PlainAgent extends BaseAgent
{
	public $JobStatus;

	public function __construct()
	{
		$this->JobStatus=Job\JOB_STATUS_INVALID;
	}

	protected function getJobStatus(): int
	{
		return $this->JobStatus;
	}

	public function run($oJob, array $aArguments = [])
	{
		$this->JobStatus=Job\JOB_STATUS_LOADED;
		//...
		$this->JobStatus=Job\JOB_STATUS_RUNNING;
		try
		{
			$oJob->run();
		}
		catch (\Throwable $th)
		{
			$this->JobStatus=Job\JOB_STATUS_EXCEPTION;
			return;
		}
		$this->JobStatus=Job\JOB_STATUS_CLOSED;
		//...
		$this->JobStatus=Job\JOB_STATUS_DONE;
		return;
	}
}