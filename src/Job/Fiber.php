<?php
/**
 * $BaseName$
 * $Id$
 *
 * DESCRIPTION
 *  A Core file for Aether.sh
 *
 * @link http://nxsys.org/spaces/aether
 * @link https://onx.zulipchat.com
 *
 * @package Aether
 * @subpackage System
 * @license http://nxsys.org/spaces/aether/wiki/license
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2018 Nexus Systems, inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

/** @namespace Native Namespace */
namespace NxSys\Toolkits\Parallax\Job;

/** Local Project Dependencies **/
use NxSys\Toolkits\Parallax;

/** Framework Dependencies **/


/** Library Dependencies **/
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;


/**
 * Implements a nominally cooperativly scheduled thread.
 * IE this "thread" should not run its own loops and instead periodically
 * check for interuption signals.
 *
 * @throws NxSys\Toolkits\Parallax\IException Well, does it?
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 */
abstract class Fiber extends BaseJob
{
	public $bIsSleep=false;
	public $bIsHalted=false;

	final public function run()
	{
		// echo "Fiber start";
		$this->initConstants();
		$this->onStartup();
		// var_dump($this->isRunning());
		do
		{
			if(!$this->isSleep())
			{
				try
				{
					// echo "Begin work";
					$this->work();
				}
				catch (\Throwable $e)
				{
					print $this->showException($e);
					$this->setException($e);
				}
			}
			$bContinue=!$this->isHalted();
		}
		while(true==$bContinue);
		$this->onShutdown();
		return;
	}

	public function onStartup() {}
	public function onShutdown() {}

	public function sleep($bStatus=true): bool
	{
		return $this->bIsSleep=$bStatus;
	}

	public function halt(): bool
	{
		return $this->bIsHalted=true;
	}

	protected function isSleep(): bool
	{
		return (bool)$this->bIsSleep;
	}

	protected function isHalted(): bool
	{
		return (bool)$this->bIsHalted;
	}



	protected abstract function work();
}
