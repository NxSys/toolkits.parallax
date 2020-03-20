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
		$this->setAutoloaderPath();
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__));
	}

	/**
	 * Sets the value of the stub autoloader
	 *
	 * You may overide it to load you own code into the stub.
	 * This is supported as long as your file returns 'true'!
	 *
	 * @param string $sPath path to autoload.php
	 * @return void
	 * @throws if the stub has be executed already
	 **/
	public function setAutoloaderPath(string $sPath = 'vendor/autoload.php')
	{
		if($this->hProcessHost)
		{
			throw new Exception("You may not redeclare the autoloader after the stub has been executed.");
		}
		// We're not going to check is_readable, on the off chance that the stub
		//  can get to the file "better" then we can...
		$this->sStubAutoloaderPath=$this->aJobEnv['PARALLAX_JOB_LOADERPATH']=$sPath;
	}

	/**
	 * Returns the configured autoloader path
	 *
	 * @return string
	 **/
	public function getAutoloaderPath(): string
	{
		return $this->sStubAutoloaderPath;
	}

	/**
	 * Sets logfile the process stub will use
	 *
	 * Defaults to <ini(error_log)>/DEFAULT_PROCESS_LOGNAME
	 * @param string $sFile path inc filename of log
	 */
	public function setJobLogFileName(string $sFile=null)
	{
		$this->aJobEnv['PARALLAX_JOB_LOGFILE']=$sFile?:dirname(ini_get('error_log')).DIRECTORY_SEPARATOR.self::DEFAULT_PROCESS_LOGNAME;
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
		$this->oInChannel->setId($this->getAgentId().'_I');
	}

	protected function setOutChannel(Parallax\Channel\IChannel $oChannel=null)
	{
		if($this->oOutChannel)
		{
			$this->oOutChannel->close();
		}
		$this->oOutChannel=$oChannel?:new $this->getChannelType();
		#is this supported??
		$this->oInChannel->setId($this->getAgentId().'_O');
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
	 * 	--3) Connect to passed channels--
	 * 	4) Initialze Job
	 * 	--4A)	Pass channel refer--
	 *
	 * @return string The code block
	 * .
	 * .
	 **/
	public function generateStubBlock(): string
	{	$sCode = <<< 'CODEBLOCK'
		<?php
		namespace NXS_PARALLAX_STUB; use ReflectionClass;
		//...debug setup
		define('PARALLAX_JOB_DEBUG', getenv('PARALLAX_JOB_DEBUG'));
		define('PARALLAX_JOB_LOGFILE', getenv('PARALLAX_JOB_LOGFILE')?: 'php://stderr');
		function _L($m){error_log($ln=sprintf("[%s][%08d] %s\n", date('c'), getmypid(), $m), 3, PARALLAX_JOB_LOGFILE);print $ln;}
		//...startup
		!PARALLAX_JOB_DEBUG ?: _L('Launching...');
		!PARALLAX_JOB_DEBUG ?: _L(sprintf('Log: %s, Loader: %s, Job: %s', getenv('PARALLAX_JOB_LOGFILE'), getenv('PARALLAX_JOB_LOADERPATH'), getenv('PARALLAX_JOB_CLASS')));
		const EMSG_NOLOADER='Job Launch Failure: Unable to read job loader ';
		const EMSG_NOJOB='Job Launch Failure: Loader is unable to load job class ';
		const EMSG_JOBEX='Job Launch Failure: Job failed with exception ';
		const PARALLAX_CLIENT_TYPE='Process';
		try
		{
			!PARALLAX_JOB_DEBUG ?: _L('Accessing loader...');
			if(!is_readable(getenv('PARALLAX_JOB_LOADERPATH')))
			{
				die(EMSG_NOLOADER.getenv('PARALLAX_JOB_LOADERPATH'));
			}
			require_once getenv('PARALLAX_JOB_LOADERPATH');
			!PARALLAX_JOB_DEBUG ?: _L('Loader accessed.');
			!PARALLAX_JOB_DEBUG ?: _L('Loading job class...');
			//init job
			if(!class_exists(getenv('PARALLAX_JOB_CLASS')))
			{
				// just bail now
				die(EMSG_NOJOB.getenv('PARALLAX_JOB_CLASS'));
			}
			!PARALLAX_JOB_DEBUG ?: _L('Job class loaded.');
			!PARALLAX_JOB_DEBUG ?: _L('Starting job... ');
			//new is not smart enough...
			$oJob=(new ReflectionClass(getenv('PARALLAX_JOB_CLASS')))->newInstanceWithoutConstructor();
			$ret=$oJob->run();
			// quiescence?
			usleep(250*1000);
			!PARALLAX_JOB_DEBUG ?: _L('Job exited.');
		}
		catch (Throwable $e)
		{
			//@todo: ex handling standard?
			_L(sprint('%s%s %s'.PHP_EOL, EMSG_JOBEX, get_class(), $e->getMessage()));
			!PARALLAX_JOB_DEBUG ?: _L(print_r($e, true));
			!PARALLAX_JOB_DEBUG ?: _L(print_r(getenv(), true));
		}
		//...finished
		!PARALLAX_JOB_DEBUG ?: _L('Exiting...');
		!PARALLAX_JOB_DEBUG ?: sleep(5);
		return $ret;
		CODEBLOCK;
		#interpolation?
		return $sCode;
	}

	public function loadStubProcess(bool $bResetProcess=false)
	{
 		if($this->hProcessHost && !$bResetProcess)
		{
			//its already ready already
			return false;
		}
		elseif(isset($this->hProcessHost) && $bResetProcess)
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

		//@TODO: Open channels

		$this->aJobEnv['PARALLAX_CH_IN']=base64_encode(serialize($this->oInChannel));
		$this->aJobEnv['PARALLAX_CH_OUT']=base64_encode(serialize($this->oOutChannel));

		//set it up
		// die(print_r(array_merge($_ENV, $this->aJobEnv), true));
		$this->aJobEnv['PARALLAX_JOB_DEBUG']=true;
		$this->hProcessHost=new sfProcess\PhpProcess(
			$this->generateStubBlock(),
			getcwd(),
			array_merge($_ENV, $this->aJobEnv)
		);
		//start it up
		codecept_debug('starting...');
		codecept_debug($this->aJobEnv);
		try
		{
			$this->hProcessHost->run();
		}
		catch (ProcessFailedException $ex)
		{
			codecept_debug('failed?');

			throw new ParallaxRuntimeException_ProcessAgent_LaunchFailure('Process did not launch successfully. Internal exception', $ex);
		}

		//it should be waiting....
		if(!$this->hProcessHost->isRunning())
		{
			// REF: sfProc will store stdout and stderr at
			// "%TEMP%\sf_proc_00.out" and "%TEMP%\sf_proc_00.err" respectively
			throw new ParallaxRuntimeException_ProcessAgent_LaunchFailure('Process exited imediantly or did not launch successfully.');
		}

		#@todo imediate check in required

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

	protected function getJobStatus(): int
	{
		$iStatus=$this->hProcessHost->getStatus();
		//convert?
		return $iStatus;
	}

	// protected function setJobStatus($sStatus): bool
	// {
	// 	//use different s/c is job is Agent aware?
	// 		#should ProcAgent jobs be allowed to use signals??
	// 	switch ($sStatus)
	// 	{
	// 		case \NxSys\Toolkits\Parallax\Job\JOB:
	// 			$this->hProcessHost->stop();
	// 			break;

	// 		default:
	// 			# code...
	// 			break;
	// 	}
	// }

}
class ParallaxRuntimeException_ProcessAgent_LaunchFailure extends RuntimeException implements Parallax\IException
{}