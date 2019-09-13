<?php
/**
 * $BaseName$
 * $Id$
 *
 * DESCRIPTION
 *  A Core file for Parallax
 *
 * @link http://nxsys.org/spaces/onx
 * @link https://onx.zulipchat.com
 *
 * @package ONX
 * @subpackage Parallax
 * @license http://nxsys.org/spaces/onx/wiki/license
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2019 Nexus Systems, inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

/** @namespace Native Namespace */
namespace NxSys\Toolkits\Parallax\Channel;

/** Local Project Dependencies **/

use InvalidArgumentException;
use LogicException;
use NxSys\Toolkits\Parallax;

/** Framework Dependencies **/


/** Library Dependencies **/
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;
use RuntimeException;

abstract class BaseChannel implements IChannel
{
	/** @var string $sId Global id for channel */
	public $sId = null;

	/** @var string $sEndpointId Id of this channel enpoint. Helps in fannout use cases */
	protected $sEndpointId = null;

	/** @var array $aSeenEndpoints List of epids that have been observed */
	protected $aSeenEndpoints = [];

	/** @var int $iChannelMode MODE_HOST */
	protected $iChannelMode = null;

	/** @var bool $bIsThisChannelHosting Is this object responsible for allocating channel resources? */
	protected $bIsThisChannelHosting;

	/**
	 * @* @param $sId string Id
	 */
	public function setId(string $sId=null)
	{
		if (!$this->sId)
		{
			$this->sId=$sId?:sprintf('%08d', getmypid()).spl_object_hash($this);
		}
		else
		{
			//perm ids
			throw new InvalidArgumentException;
		}
		return;
	}

	/**
	 * @return string The Id
	 */
	public function getId(): string
	{
		if (!$this->sId)
		{
			throw new InvalidArgumentException;
		}
		return $this->sId;
	}

	abstract public function _sendMessage(Message $oMsg);
	abstract public function _recvMessage(): Message;

	public function setMyEndpointId(string $sId)
	{
		$this->sEndpointId=$sId;
	}
	public function getMyEndpointId(): string
	{
		return $this->sEndpointId;
	}

	public function getKnownEndpoints(): array
	{
		return $this->aSeenEndpoints;
	}

	protected function addSeenEndpoint(string $sId)
	{
		$this->aSeenEndpoints[]=$sId;
	}

	abstract public function detectMode();

	function getMode(): int
	{
		return $this->iChannelMode;
	}

	public function getEnv(): array
	{
		return (array) $this->set(MESSAGE_CTRL_ENV_GET, [], null, MESSAGE_TYPE_CTRL);
	}
	function mergeEnv(array $aData)
	{
		$this->set(MESSAGE_CTRL_ENV_MERGE, $aData, null, MESSAGE_TYPE_CTRL);
	}	
	public function get($sName)
	{
		$oMsg=new Message;
		$oMsg->iType=MESSAGE_TYPE_DATA;
		$oMsg->sLabel=$sName;
		return $this->_sendMessage($oMsg);
	}
	public function set($sName, $mValue, $sTargetEndpointId=null, int $iType=MESSAGE_TYPE_DATA)
	{
		$oMsg=new Message;
		$oMsg->iType=$iType;
		$oMsg->sLabel=$sName;
		$oMsg->mValue=$mValue;
		return $this->_sendMessage($oMsg, $sTargetEndpointId);
	}
	public function registerRemoteFunction(string $sFuncName=null, callable $hFunc)
	{
		// $this->set(MESSAGE_CTRL_ENV_MERGE, $aData, null, MESSAGE_TYPE_CTRL);
	}

	protected function callInbound()
	{
		# code...
	}

	#outbound
	public function __call(string $sMeth, array $aParams)
	{
		//meth must be prefixed with callRemote
		$this->set(MESSAGE_CTRL_RPC_CALL, [$sMeth, $aParams], null, MESSAGE_TYPE_CTRL);
	}

}
