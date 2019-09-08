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
use Symfony\Component\Process\Exception\ProcessFailedException;


/**
 *
 */
class ProcessAgent extends BaseAgent
{
	public $aCoreVars;

	const COMPOSER_CLASSLOADER_CLASSPATH='Composer\Autoload\ClassLoader';

	public function __construct()
	{
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__));
		
		//40 char string
		$sAgentId=sprintf('%08d', getmypid()).spl_object_hash($this);
		$this->aCoreVars['PARALLAX_AGENT_ID']=$sAgentId;
		$this->aCoreVars['PARALLAX_CHANNEL_TYPE']=$sAgentId;
		$this->aCoreVars['PARALLAX_PROCESS_LOGFILE']=dirname(ini_get('error_log')).DIRECTORY_SEPARATOR.'parallax_process.log';
		
		

		$this->oInData=new Thread_Channel(DEFAULT_CHANNEL_CAPACITY);
		$this->oOutData=new Thread_Channel(DEFAULT_CHANNEL_CAPACITY);
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__));
		$this->cExecute = function ($oJob, Thread_Channel $oInData, Thread_Channel $oOutData, $aArguments = [])
		{
		};
	}

	/**
	 * Generates (return) Stub Code
	 *
	 * Returns code that is to be run as process stub.
	 * Has mechanisms that 
	 * 	1) Read params from env
	 * 	2) Enable Code Autoloading
	 * 	3) Initialze Job
	 * 	3) Connect to passed channels
	 * 	3A)	Pass channel refer 
	 *
	 * @param Type $var Description
	 * @return types
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
		define('PARALLAX_JOB_FALLBACKPATH', getenv('PARALLAX_JOB_FALLBACKPATH'));
		try
		{
			$oCL=require_once getenv('PARALLAX_AL_PATH');
			var_dump($oCL->findFile(PARALLAX_JOB_CLASS));
			if(!$oCL->findFile(PARALLAX_JOB_CLASS))
			{
				error_log(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n",  __FILE__, __FUNCTION__, __LINE__).PHP_EOL, 4);
				$oCL->addClassMap([PARALLAX_JOB_CLASS => PARALLAX_JOB_FALLBACKPATH]);
				// require_once PARALLAX_JOB_FALLBACKPATH;
			}
			if(!$oCL->loadClass(PARALLAX_JOB_CLASS))
			{
				error_log(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n",  __FILE__, __FUNCTION__, __LINE__).PHP_EOL, 4);
				#just bail now
				throw new InvalidArgumentException;
			}
			#new is not smart
			$oJob=(new ReflectionClass(PARALLAX_JOB_CLASS))->newInstance(); 
			// $oJob->setInputChannel($oInData);
			// $oJob->setOutputChannel($oOutData);
			// $oJob->initialize();
			// return $oJob->run($aArguments);
		}
		catch (Throwable $e)
		{
			error_log(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n",  __FILE__, __FUNCTION__, __LINE__).PHP_EOL, 4);
			error_log(print_r($e, true).PHP_EOL, 4);
			error_log(print_r(getenv(), true).PHP_EOL, 4);
		}
		sleep(5);
		CODEBLOCK;
		return $sCode;
	}

	public function run($sJob, array $aArguments = [])
	{
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		// Okay... first we're going to see if conventional means can be used to find the Job class
		if (   !is_string($sJob)
			|| !$this->isJobClassLocatable($sJob))
		{
			//No? Well maybe the invoker has provided the means
			// (unique composer instance?) to find the Job class?
			if(	   !isset($aArguments['PARALLAX_AL_PATH'])
				|| !is_readable($aArguments['PARALLAX_AL_PATH'])
				|| !(include $aArguments['PARALLAX_AL_PATH'])->findFile($sJob)
				)
			// Guess not...
			throw new ParallaxRuntimeException_ProcessAgent_LaunchFailure(
				'Unable to locate '.(string) $sJob.' Does Composer know how to find it?'
			);
		}
		$this->aCoreVars['PARALLAX_AL_PATH']  =$this->resolveComposerAutoloaderPath();
		$this->aCoreVars['PARALLAX_JOB_CLASS']=$sJob;
		$this->aCoreVars['PARALLAX_JOB_FALLBACKPATH']=(new ReflectionClass($sJob))->getFileName();
		$proc=new sfProcess\PhpProcess(
			$this->generateStubBlock(),
			getcwd(), 
			array_merge($_ENV, $this->aCoreVars, $aArguments)
		);
		// codecept_debug($sJob);
		// codecept_debug($proc);
		try
		{
			$proc->run();
		}
		catch (ProcessFailedException $ex)
		{
			throw new ParallaxRuntimeException_ProcessAgent_LaunchFailure('Process did not launch successfully. Internal exception', $ex);
		} 
		if(	  !$proc->isRunning() #ehhh....
			|| $proc->isTerminated())
		{
			// REF: sfProc will store stdout and stderr at
			// "%TEMP%\sf_proc_00.out" and "%TEMP%\sf_proc_00.err" respectively
			throw new ParallaxRuntimeException_ProcessAgent_LaunchFailure('Process did not launch successfully.');
		}
		// $oResult = $this->hThreadRuntime->run($this->cExecute, [$oJob, $this->oInData, $this->oOutData, $aArguments]);
		(printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__));
		return null;
	}

	protected function resolveComposerAutoloaderPath(): string
	{
		// 1) find path
		// C:\dev\projects\onx\parallax\vendor\composer\ClassLoader.php
		$sComLdrPath=(new ReflectionClass(self::COMPOSER_CLASSLOADER_CLASSPATH))->getFileName();
		if(false==$sComLdrPath)
		{
			// then.... how did you load **me**?!?1
			throw new ParallaxRuntimeException_ProcessAgent_LaunchFailure('Unable to locate Composer\Autoload\ClassLoader');
		}
		
		// C:\dev\projects\onx\parallax\vendor
		$sComPath=dirname($sComLdrPath, 2 /* remove composer\ClassLoader.php */);
		$sComPath.=DIRECTORY_SEPARATOR.'autoload.php';
		if(!is_readable($sComPath))
		{
			// again ... wut!?
			throw new ParallaxRuntimeException_ProcessAgent_LaunchFailure('Unable to locate global autoload. Where is '.$sComPath.'?');			
		}
		return $sComPath;		
	}

	protected function locateJobClass($sJobClassName): string
	{
		// 1) find path
		// MAGIC!
		// 3) get instance
		$oComposerLoader=include $this->resolveComposerAutoloaderPath();
		return $oComposerLoader->findFile($sJobClassName);
	}
	protected function isJobClassLocatable($sJobClassName): bool
	{
		return (bool) $this->locateJobClass($sJobClassName);
	}

	
}
class ParallaxRuntimeException_ProcessAgent_LaunchFailure extends RuntimeException implements Parallax\IException
{}