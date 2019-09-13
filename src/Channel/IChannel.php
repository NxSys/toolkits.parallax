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

use Serializable;

const MODE_HOST='host';

interface IChannel extends Serializable //esc?
{
	const MODE_HOST=MODE_HOST;

	// function configure(string $sId, array $aConfig);

	function open();
	function close();

	function setId(string $sId=null);
	function getId(): string;

	#for rpcs mostly
	function setMyEndpointId(string $sId);
	function getMyEndpointId(): string;
	function getKnownEndpoints(): array;

	#figure out if this has to create the chan
	function detectMode();
	function getMode(): int;

	function getEnv(): array;
	function mergeEnv(array $aData);

	// function sendJob(string $sJobClassname);
	// function ctrlRunJob(string $sJob, array $aArguments);

	function get($sName);
	function set($sName, $mValue, $sTargetEndpointId=null, int $iType=MESSAGE_TYPE_DATA);
	function registerRemoteFunction(string $sFuncName=null, callable $hFunc);
	function __call(string $sMeth, array $aParams); //for rpc magic
}