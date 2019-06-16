<?php
/**
 * $BaseName$
 * $Id$ rce.php 104 2018-02-23 23:44:25Z nxs.cfeamster $ rce.php 104 2018-02-23 23:44:25Z nxs.cfeamster $ rce.php 104 2018-02-23 23:44:25Z nxs.cfeamster $ rce.php 104 2018-02-23 23:44:25Z nxs.cfeamster $ rce.php 104 2018-02-23 23:44:25Z nxs.cfeamster $ rce.php 104 2018-02-23 23:44:25Z nxs.cfeamster $ rce.php 104 2018-02-23 23:44:25Z nxs.cfeamster $ rce.php 104 2018-02-23 23:44:25Z nxs.cfeamster $
 *
 * DESCRIPTION
 *  Back Connector for RCEs * @copyright Copyright 2018 Nexus Systems, inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */
declare(strict_types=1);

/** @namespace Native Namespace */
namespace NxSys\Toolkits\Parallax;

/** Local Project Dependencies **/
use NxSys\Toolkits\Parallax;

/** Framework Dependencies **/


/** Library Dependencies **/
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;


/**
 * Undocumented interface
 *
 * Why does this exist? What does this do?
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 */
 interface IAgentModel
 {
	function setExecutionStrategy();
	function setWoStrategy();

	function addWork();
	function getResult();


 }
