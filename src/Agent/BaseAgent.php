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
use NxSys\Toolkits\Parallax\Job\BaseJob;
use NxSys\Toolkits\Parallax\Job\Fiber;

/** Framework Dependencies **/
use parallel\Runtime as Thread_Runtime;
use parallel\Channel as Thread_Channel;

//....
use SplQueue;
use Exception;
use Throwable;
use Closure;

/** Library Dependencies **/
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;

const DEFAULT_CHANNEL_CAPACITY = 1024;
const THREAD_ENVIRONMENT_STUB =
	__DIR__.DIRECTORY_SEPARATOR
	.'..'.DIRECTORY_SEPARATOR
	.'ThreadBooter.php';

/**
 *
 */
class BaseAgent
{
	protected $hThreadRuntime = False;

	public function __construct()
	{
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__));
		if (!$this->hThreadRuntime)
		{
			$this->hThreadRuntime=new Thread_Runtime(THREAD_ENVIRONMENT_STUB);
		}
		$this->oInData=new Thread_Channel(DEFAULT_CHANNEL_CAPACITY);
		$this->oOutData=new Thread_Channel(DEFAULT_CHANNEL_CAPACITY);
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__));
		$this->cExecute = function ($oJob, Thread_Channel $oInData, Thread_Channel $oOutData, $aArguments = [])
		{
			var_dump($oJob);
			//die(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__));
			error_log(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__).PHP_EOL, 4);
			//var_dump(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__).PHP_EOL);
			$oJob->setInputChannel($oInData);
			$oJob->setOutputChannel($oOutData);
			$oJob->initialize();
			try
			{
				return $oJob->run($aArguments);
			}
			catch (\Throwable $e)
			{
				var_dump($e);
			}
		};
	}


	public function start(BaseJob $oJob, array $aArguments = [])
	{
		var_dump($oJob);
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		$oResult = $this->hThreadRuntime->run($this->cExecute, [$oJob, $this->oInData, $this->oOutData, $aArguments]);
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		return $oResult;
	}

}
