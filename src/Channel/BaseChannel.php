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
// use NxSys\Toolkits\Parallax\Channel\ParallaxChannel_InvalidParameterException;
use RuntimeException;

/**
 * @var string URN ... for Channel Resource Name `urn:nxsys-ipcrn:<IPC_NSS:[name]:[CRID]>`
 */
const IPC_CRN_NID='nxsys-ipcrn';


abstract class BaseChannel implements IChannel
{
	/** @var string $sId Global id for channel */
	public $sId = null;

	/** @var array $aConfig settings require for setup */
	protected $aConfig = [];

	/** @var int $iChannelMode MODE_HOST */
	protected $iChannelMode = null;

	/** @var bool $bIsThisChannelHosting Is this object responsible for allocating channel resources? */
	protected $bIsThisChannelHosting;

	const CRN_NID=IPC_CRN_NID;

	//child class needs to set IPC_CRN_NSS;

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
			// throw new ParallaxChannel_InvalidParameterException('Channel IDs must not be set twice.');
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
			throw new ParallaxChannel_InvalidParameterException('There is no id. Have you called ::startup() or ::setId()?');
		}
		return $this->sId;
	}

	public function getChannelResourceName(): string
	{
		// <driver>:<channelId>?<opts>
		$sCRNfmt='%s:%s?%s';
		ksort($this->aConfig);
		$sCRNData=http_build_query($this->aConfig);
		// $this->shutdown(); why should this have side effects
		return sprintf($sCRNfmt,
					sprintf('urn:%s:%s', self::CRN_NID, $this::CRN_NSS),
						$this->getId(), $sCRNData);
	}

	public function openChannelResourceName(/*string*/ $sCRN)
	{
		//check type
		$aCRN=parse_url($sCRN);
		if (!$aCRN)
		{
			throw new ParallaxChannel_InvalidParameterException("Error Processing Request", 1);
		}

		//check CRN standard

		//should be "well formed"
		$this->startup($aCRN);
		return; //all done
	}
	abstract protected function startup(array $aCRN);
	abstract protected function shutdown();

	public function setMessageBufferCount(int $iMessageCount=1)
	{
		if($iMessageCount< -1 || $iMessageCount > PHP_INT_MAX)
		{
			throw new ParallaxChannel_InvalidParameterException('Message count is invalid.');
		}
		$this->iMsgBuff=$iMessageCount;
	}

	public function getMessageBufferCount(): int
	{
		return $this->iMsgBuff;
	}

	public function send($sName)
	{
		$oMsg=new Message;
		$oMsg->sLabel=$sName;
		return $this->_sendMessage($oMsg);
	}
	public function set($sName, $mValue)
	{
		$oMsg=new Message;
		$oMsg->sLabel=$sName;
		$oMsg->mValue=$mValue;
		return $this->_sendMessage($oMsg);
	}

	function __destruct()
	{
		$this->close();
	}
}
