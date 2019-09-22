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

interface IRpcChannel extends IChannel
{
	function setMyEndpointId(string $sId);
	function getMyEndpointId(): string;
	function getKnownEndpoints(): array;

	function registerRemoteFunction(string $sFuncName=null, callable $hFunc);
	function __call(string $sMeth, array $aParams); //for rpc magic
}