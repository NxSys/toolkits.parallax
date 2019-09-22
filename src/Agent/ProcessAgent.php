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
use RuntimeException;
use ReflectionClass;

/** Library Dependencies **/
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;
use Symfony\Component\Process as sfProcess;

use Composer\Autoload\ClassLoader as ComposerLoader;
use NxSys\Toolkits\Parallax\Channel;
use Symfony\Component\Process\Exception\ProcessFailedException;


/**
 *
 */
class ProcessAgent extends BaseAgent
{
	public $aJobEnv=[];

	/** @var Parallax\Channel\IChannel $oInChannel In Channel */
	protected $oInChannel;

	/** @var Parallax\Channel\IChannel $oOutChannel Out Channel */
	protected $oOutChannel;

	/** @var String $sChannelType FQCN of Channel Type to use */
	protected $sChannelType;


	/**
	 * @var sfProcess\PhpProcess
	 */
	public $hProcessHost=false;

	const COMPOSER_CLASSLOADER_CLASSPATH='Composer\Autoload\ClassLoader';
	const DEFAULT_PROCESS_LOGNAME='parallax_process.log';

	public function __construct()
	{
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__));
		$this->setAgentId();
		$this->setJobLogFileName();
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__));
	}

	/**
	 * Sets logfile the process stub will use
	 * 
	 * Defaults to <ini(error_log)>/DEFAULT_PROCESS_LOGNAME
	 * @param string $sFile path inc filename of log
	 */
	public function setJobLogFileName(string $sFile=null)
	{
		$this->aJobEnv['PARALLAX_PROCESS_LOGFILE']=$sFile?:dirname(ini_get('error_log')).DIRECTORY_SEPARATOR.self::DEFAULT_PROCESS_LOGNAME;
	}

	public function setAgentId(string $sId=null)
	{
		//40 char string
		return $this->aJobEnv['PARALLAX_AGENT_ID']=$sId?:sprintf('%08d', getmypid()).spl_object_hash($this);
	}

	public function getAgentId()
	{
		return $this->aJobEnv['PARALLAX_AGENT_ID'];
	}

	public function setChannelType(string $sChannelClassName)
	{
		$this->sChannelType=$sChannelClassName;
	}

	public function getChannelType(): string
	{
		return $this->sChannelType;
	}

	protected function setInChannel(Parallax\Channel\IChannel $oChannel=null)
	{
		if($this->oInChannel)
		{
			$this->oInChannel->close();
		}
		$this->oInChannel=$oChannel?:new $this->getChannelType();
	}

	protected function setOutChannel(Parallax\Channel\IChannel $oChannel=null)
	{
		if($this->oOutChannel)
		{
			$this->oOutChannel->close();
		}
		$this->oOutChannel=$oChannel?:new $this->getChannelType();
	}

	public function getInChannel(): Parallax\Channel\IChannel
	{
		return $this->oInChannel;
	}

	public function getOutChannel(): Parallax\Channel\IChannel
	{
		return $this->oOutChannel;
	}

	/**
	 * Generates (return) Stub Code
	 *
	 * Returns code that is to be run as process stub.
	 * Has mechanisms that 
	 * 	1) Read params from env
	 * 	2) Enable Code Autoloading
	 * 	3) Connect to passed channels
	 * 	4) Initialze Job
	 * 	4A)	Pass channel refer 
	 *
	 * @param Type $var Description
	 * @return string
	 * @throws conditon
	 **/
	public function generateStubBlock(Type $var = null): string
	{
		$sCode = <<< 'CODEBLOCK'
		<?php
		sleep(5);
		error_log(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n",  __FILE__, __FUNCTION__, __LINE__).PHP_EOL, 4);
		
		define('PARALLAX_AGENT_ID', getenv('PARALLAX_AGENT_ID'));
		define('PARALLAX_JOB_CLASS', getenv('PARALLAX_JOB_CLASS'));
		
		try
		{
			$oInChan=unserialize(getenv('PARALLAX_CH_IN'));
			$oOutChan=unserialize(getenv('PARALLAX_CH_OUT'));
			
			//now enter job loop
				//get new job settings
				//init job
			require_once getenv('PARALLAX_JOB_LOADERPATH');
			if(!class_exists(PARALLAX_JOB_CLASS))
			{
				error_log(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n",  __FILE__, __FUNCTION__, __LINE__).PHP_EOL, 4);
				#just bail now
				throw new InvalidArgumentException;
			}
			#new is not smart enough...
			$oJob=(new ReflectionClass(PARALLAX_JOB_CLASS))->newInstanceWithoutConstructor(); 
			// $oJob->setInputChannel($oInData);
			// $oJob->setOutputChannel($oOutData);
			// $oJob->initialize();
			// return $oJob->run($aArguments);
			// quiescence?
		}
		catch (Throwable $e)
		{
			error_log(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n",  __FILE__, __FUNCTION__, __LINE__).PHP_EOL, 4);
			error_log(print_r($e, true).PHP_EOL, 4);
			error_log(print_r(getenv(), true).PHP_EOL, 4);
		}
		sleep(5);
		CODEBLOCK;
		#interpolation?
		return $sCode;
	}

	public function loadStubProcess(bool $sResetProcess=false)
	{
 		if($this->hProcessHost && !$sResetProcess)
		{
			//its already ready already
			return false;
		} 
		elseif(isset($this->hProcessHost) && $sResetProcess)
		{
			//terminate & unset
			$this->hProcessHost->stop(0);
			unset($this->hProcessHost);
		}

		//channels?
		if (!$this->oInChannel)
		{
			$this->setInChannel(new Parallax\Channel\InMemoryChannel);
		}
		if (!$this->oOutChannel)
		{
			$this->setOutChannel(new Parallax\Channel\InMemoryChannel);
		}
		codecept_debug('channel');
		codecept_debug($this->oInChannel);

		$this->aJobEnv['PARALLAX_CH_IN']=serialize($this->oInChannel);
		$this->aJobEnv['PARALLAX_CH_OUT']=serialize($this->oOutChannel);

		//set it up
		$this->hProcessHost=new sfProcess\PhpProcess(
			$this->generateStubBlock(),
			getcwd(),
			array_merge($_ENV, $this->aJobEnv)
		);
		//start it up
		try
		{
			$this->hProcessHost->run();
		}
		catch (ProcessFailedException $ex)
		{
			throw new ParallaxRuntimeException_ProcessAgent_LaunchFailure('Process did not launch successfully. Internal exception', $ex);
		}

		//it should be waiting....
		if(!$this->hProcessHost->isRunning())
		{
			// REF: sfProc will store stdout and stderr at
			// "%TEMP%\sf_proc_00.out" and "%TEMP%\sf_proc_00.err" respectively
			throw new ParallaxRuntimeException_ProcessAgent_LaunchFailure('Process did not launch successfully.');
		}

		return true;
	}

	public function run($sJob, array $aArguments = [])
	{
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		// $this->aJobEnv['PARALLAX_JOB_LOADERPATH']=$this->resolveComposerAutoloaderPath();
		$this->aJobEnv['PARALLAX_JOB_CLASS']=$sJob;		
		
		$this->loadStubProcess();
		//channels are available now
		$this->oInChannel->mergeEnv($this->aJobEnv);
		$this->oInChannel->runJob($sJob, $aArguments);
		


		// $oResult = $this->hThreadRuntime->run($this->cExecute, [$oJob, $this->oInData, $this->oOutData, $aArguments]);
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		return null;
	}

}
class ParallaxRuntimeException_ProcessAgent_LaunchFailure extends RuntimeException implements Parallax\IException
{}