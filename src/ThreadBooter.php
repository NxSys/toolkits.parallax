<?php

//stuff

declare(strict_types=1);

/** @namespace Native Namespace */
namespace NxSys\Toolkits\Parallax;
use LogicException;

//define some consts
define ('JOB_ID', uniqid((string) random_int((int)(PHP_INT_MAX/8), PHP_INT_MAX)));
const DEFAULT_CHANNEL_CAPACITY = 1024;

//use a basic AL
#@todo use the "nondumb" default AL for phars?
set_include_path(get_include_path().PATH_SEPARATOR.'.'); //just in case @todo perf hit if dupped?
spl_autoload_register();

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
		static $oInstance;
		if($oInstance)
		{
			throw new InvalidJobOperation_RedudantBoot("You can only boot a job once per engine instance.");
		}
		$oInstance=new self;
		# code...
	}
}
// require_once 'IException.php';
class InvalidJobOperation_RedudantBoot extends LogicException implements IException {}