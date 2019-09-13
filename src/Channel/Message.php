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
 * @author Chris R. Feamster <cfeamster@nxs.systems>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

/** @namespace Native Namespace */
namespace NxSys\Toolkits\Parallax\Channel;

/** Ambient Constants */
const MESSAGE_TYPE_INVALID	= 0;
const MESSAGE_TYPE_DATA 	= 1;
const MESSAGE_TYPE_CTRL 	= 2;

const MESSAGE_CTRL_JOB_LAUNCH	= 10;
const MESSAGE_CTRL_ENV_GET  	= 20;
const MESSAGE_CTRL_ENV_MERGE	= 25;
const MESSAGE_CTRL_RPC_CALL 	= 30;
const MESSAGE_CTRL_EPT_APPEAR	= 40;
const MESSAGE_CTRL_EPT_DISAPPEAR= 45;

/**
 * Message
 */
class Message  
{
	const TYPE_INVALID = MESSAGE_TYPE_INVALID;
	const TYPE_DATA = MESSAGE_TYPE_DATA;
	const TYPE_CTRL = MESSAGE_TYPE_CTRL;

	const CTRL_JOB_LAUNCH	= MESSAGE_CTRL_JOB_LAUNCH;
	const CTRL_ENV_GET  	= MESSAGE_CTRL_ENV_GET;
	const CTRL_ENV_MERGE	= MESSAGE_CTRL_ENV_MERGE;
	const CTRL_RPC_CALL 	= MESSAGE_CTRL_RPC_CALL;
	const CTRL_EPT_APPEAR	= MESSAGE_CTRL_EPT_APPEAR;
	const CTRL_EPT_DISAPPEAR= MESSAGE_CTRL_EPT_DISAPPEAR;
	
	/** @var string $sLabel Lable of value to send */
	public $sLabel = null;

	/** @var mixed $mValue Value to send */
	public $mValue = null;

	/** @var int $iType Type of message */
	public $iType = TYPE_INVALID;
}
