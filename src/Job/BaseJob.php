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

//....
use SplQueue;
use Thread;
use Stackable;
use Exception;
use Throwable;


/**
 * Undocumented class
 *
 * Why does this exist? What does this do?
 *
 * @throws NxSys\Toolkits\Parallax\IException Well, does it?
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 */
// abstract class BaseJob extends CoreEsc\pthreads\Thread implements IJob
abstract class BaseJob extends Thread implements IJob
{
	protected $aLocalConstants = [];
	public function __construct()
	{
		$this->aInData=new SplQueue ;
		$this->aOutData=new SplQueue ;
	}

	public function setupConstants(array $aConstants): void
	{
		foreach ($aConstants as $name)
		{
			$this->aLocalConstants[$name]=constant($name);
		}
		return;
	}

	public function initConstants(): void
	{
		foreach ($this->aLocalConstants as $name => $value)
		{
			if (!defined($name))
			{
				define($name, $value);
			}
		}
	}

	/**
	 * Undocumented function
	 *
	 * @return mixed
	 */
	public function getReturn(): mixed
	{
		//eehh we really should be joined to do this
		static $bIsJoined;
		if(!$bIsJoined)
		{
			$this->join();
			$bIsJoined=true;
		}
		return $this->return;
	}

	protected function setReturn($mValue)
	{
		return $this->return=$mValue;
	}

	public function __tostring(): string
	{
		return (string)$this->getReturn();
	}

	protected function pushOut($mValue)
	{
		//@todo enforce simple serilization
		array_push($this->aOutData, $mValue);
		return;
	}
	public function popOut()
	{
		return array_pop($this->aOutData);
	}
	protected function unshiftOut($mValue)
	{
		array_unshift($this->aOutData, $mValue);
	}
	public function shiftOut()
	{
		array_shift($this->aOutData);
	}
	#---
	public function pushIn($mValue)
	{
		// printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__);
		// var_dump($mValue);
		//@todo enforce simple serilization
		$a=$this->aInData;
		$a->enqueue($mValue);
		// var_dump($a)
		$this->aInData = $a;
		// var_dump($this->aInData)
		return;
	}
	protected function popIn()
	{
		return array_pop($this->aInData);
	}
	public function unshiftIn($mValue)
	{
		array_unshift($this->aInData, $mValue);
	}
	protected function shiftIn()
	{
		// printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__);
		// var_dump("ShiftIn");
		// var_dump($this->aInData);
		$aTemp=$this->aInData;
		if (count($aTemp) == 0)
		{
			return null;
		}
		$ret = $aTemp->dequeue();
		// var_dump($aTemp);
		$this->aInData = $aTemp;
		// var_dump($this->aInData);
		return $ret;
	}

	public function hasIn()
	{
		return count($this->aInData) > 0;
	}

	public function hasOut()
	{
		return count($this->aOutData) > 0;
	}


	public function setException(Throwable $e)
	{
		// $this->oException=$e;
	}

	public function getException(): Throwable
	{
		return $this->oException;
	}

	public function showException(Throwable $e, $iNest=0)
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
		return $sMsg;
	}
}