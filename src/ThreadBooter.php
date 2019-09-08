<?php

//stuff

declare(strict_types=1);

/** @namespace Native Namespace */
namespace NxSys\Toolkits\Parallax;

use ErrorException;
use LogicException;
use Throwable;

error_reporting(E_ALL);

require_once 'IException.php';

//define some consts
define('JOB_ID', uniqid((string) random_int((int)(PHP_INT_MAX/8), PHP_INT_MAX)));
const DEFAULT_CHANNEL_CAPACITY = 1024;

set_include_path(__DIR__.PATH_SEPARATOR.get_include_path());

//use a basic AL
#@todo use the "nondumb" default AL for phars?
spl_autoload_register(function($class) {
        if (stripos($class, __NAMESPACE__) === 0)
        {
            @include(__DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, strtolower(substr($class, strlen(__NAMESPACE__)))) . '.php');
        }
    }
);

// require_once 'C:\dev\projects\onx\aether\applications.aether-rce\vendor\autoload.php';

//codecept_debug(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__));
//debug_print_backtrace();
//boot
ThreadBooter::boot();

//configure

//========================D=E=C=L=A=R=A=T=I=O=N=S============================//
/**
 * undocumented class
 */
class ThreadBooter
{
	const DEFAULT_CHANNEL_CAPACITY = DEFAULT_CHANNEL_CAPACITY;
	private function __constructor()
	{}

	public static function boot()
	{
		ini_set('error_log', __DIR__.DIRECTORY_SEPARATOR.'thread-engine.log');
		error_log('log 0 inited'.PHP_EOL, 0);
		error_log('log 3 inited'.PHP_EOL, 3, __DIR__.DIRECTORY_SEPARATOR.'thread-engine.log');
		error_log('log 4 inited'.PHP_EOL, 4);
		var_dump(sprintf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__));
		static $oInstance;
		if($oInstance)
		{
			throw new InvalidJobOperation_RedundantBoot("You can only boot an environment once per engine instance.");
		}
		$oInstance=new static;

		//set_error_handler([static::class, 'initialErrorHandler']);
		//set_exception_handler([static::class, 'initialExceptionHandler']);
	}

	public static function initialErrorHandler(int $errno, string $errstr, string $errfile, int $errline): bool
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__);
		// var_dump((error_reporting() & $severity));
	    if(!(error_reporting() & $errno))
	    {
    	    // This error code is not included in error_reporting
        	// but lets call it a quiet notice
        	//@todo emit notice
			printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__);
        	return true;
    	}
		printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __METHOD__, __LINE__);
		$ex=new ErrorException($errstr, $errno, $errfile, $errline);
		$this->showException($ex);
		return true;
	}

	public static function initialExceptionHandler(Throwable $e, int $iNest=0)
	{
		$excode=$e->getCode();
		$exmsg=$e->getMessage();

		if(property_exists($e,'xdebug_message'))
		{
			echo $e->xdebug_message."\n";
		}

		$sMsg=sprintf('Exception %s: %s (C:%d) in %s on line %d'.PHP_EOL,
					  get_class($e),$exmsg,$excode,$e->getFile(),$e->getLine());

		if($iNest)
		{
			$sMsg=str_repeat(' ', $iNest).'\->'.$sMsg;
		}
		if($e->getPrevious())
		{
			$this->showException( $e->getPrevious(), $iNest+1);
		}
		$sMsg.=sprintf('Outer Stack Trace:');
		$sMsg.=sprintf($e->getTraceAsString());
		printf($sMsg);
		return $sMsg;
	}
	/**
	 * Undocumented function
	 *
	 * @param object $oThread Shoiuld be BaseJob but that type might not be loaded yet
	 * @param array $aThreadArgs
	 * @param array $aEnvArgs
	 * @return void
	 */
	public static function _startThread(object $oThread, array $aThreadArgs, array $aEnvArgs=[])
	{
		#gen uniq thread id with atomic
		// $mReturn=$oThread->run($aThreadArgs);

		// return $mReturn;
	}

	public static function genJobId()
	{
		return uniqid((string) random_int((int)(PHP_INT_MAX/8), PHP_INT_MAX));
	}
}
// require_once 'IException.php';
class InvalidJobOperation_RedundantBoot extends LogicException implements IException {}
