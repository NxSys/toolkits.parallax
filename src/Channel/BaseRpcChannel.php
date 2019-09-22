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
use ParallaxChannel_InvalidParameterException;
use RuntimeException;

abstract class BaseRpcChannel extends BaseChannel
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
