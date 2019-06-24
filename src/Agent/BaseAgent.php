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

/**
 * 
 */
class BaseAgent 
{
	protected $hThreadRuntime = False;
	
	public function __construct()
	{
		if (!$this->hThreadRuntime)
		{
			$this->hThreadRuntime=new Thread_Runtime(__DIR__.'\..\..\vendor\autoload.php');
		}
		
		$this->oInData=new Thread_Channel(DEFAULT_CHANNEL_CAPACITY);
		$this->oOutData=new Thread_Channel(DEFAULT_CHANNEL_CAPACITY);
		
		$this->cExecute = function (BaseJob $oJob, Thread_Channel $oInData, Thread_Channel $oOutData, $aArguments = [])
		{
			$oJob->setInputChannel($oInData);
			$oJob->setOutputChannel($oOutData);
			$oJob->initialize();
			return $oJob->run($aArguments);
		};
	}
		
	
	public function run(BaseJob $oJob, array $aArguments = [])
	{
		$oResult = $this->hThreadRuntime->run($this->cExecute, [$oJob, $this->oInData, $this->oOutData, $aArguments]);
		return $oResult;
	}
	
}
