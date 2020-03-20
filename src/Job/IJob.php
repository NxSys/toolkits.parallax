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
 * Job has not been initilaized correctly.
 */
const JOB_STATUS_INVALID=null;

/**
 * Job is ready to run.
 */
const JOB_STATUS_LOADED=	100;

/**
 * Job is currently executing
 */
const JOB_STATUS_RUNNING=	200;

/**
 * Job has stoped running until told to resume
 */
const JOB_STATUS_PAUSED=	300;

/**
 * Job has encountered an excetional condition
 */
const JOB_STATUS_EXCEPTION=	500;

/**
 * Job has completed sucessfully
 */
const JOB_STATUS_DONE=		600;

/**
 * Job has been unloaded from memory
 */
const JOB_STATUS_CLOSED=	900;

/**
 * Interface for executable/runable object
 *
 * Why does this exist? What does this do?
 *
 * @throws NxSys\Toolkits\Parallax\IException Well, does it?
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 */
 interface IJob
 {
	const STATUS_INVALID=	JOB_STATUS_INVALID;
	const STATUS_LOADED=	JOB_STATUS_LOADED;
	const STATUS_RUNNING=	JOB_STATUS_RUNNING;
	const STATUS_PAUSED=	JOB_STATUS_PAUSED;
	const STATUS_EXCEPTION=	JOB_STATUS_EXCEPTION;
	const STATUS_DONE=		JOB_STATUS_DONE;
	const STATUS_CLOSED=	JOB_STATUS_CLOSED;

	//protected function run();
	// function start();
	// function isRunning();
 }
